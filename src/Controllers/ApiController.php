<?php
namespace App\Controllers;

use App\Services\SaleService;

class ApiController {
    private $saleService;
    
    public function __construct() {
        $this->saleService = new SaleService();
    }
    
    /**
     * Retorna datos del dashboard en formato JSON
     */
    public function dashboardData() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id'])) {
            $this->sendJson(['error' => 'No autenticado'], 401);
            return;
        }

        try {
            // Log fecha actual
            $fechaActual = date('Y-m-d');
            error_log("Dashboard - Fecha actual: " . $fechaActual);
            
            // Permitir forzar fecha vía query (?date=YYYY-MM-DD) para depuración
            $dateParam = isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])
                ? $_GET['date']
                : null;

            // Obtener datos del servicio (filtrado por fecha si se indicó)
            $data = $this->saleService->getDashboardData($dateParam);
            $totals = is_array($data['totals'] ?? null) ? $data['totals'] : [];
            $recent = is_array($data['recent'] ?? null) ? $data['recent'] : [];
            
            // Debug: Log data structure
            error_log("Dashboard Data: " . print_r($data, true));
            
            error_log("Totals raw: " . print_r($totals, true));
            error_log("Recent sales count: " . count($recent));

            // Obtener ventas de la semana actual
            $ventasSemana = $this->saleService->getWeeklySales();
            
            // Obtener ventas de los últimos 7 días para el gráfico
            $ventasUltimos7Dias = $this->saleService->getLast7DaysSales();
            
            // Obtener distribución de métodos de pago del día (o de la fecha indicada)
            $metodosPago = $this->saleService->getPaymentMethodsDistribution($dateParam);

            $response = [
                'ventas_dia' => $this->sanitizeFloat($totals['total_sales'] ?? 0),
                'transacciones' => $this->sanitizeInt($totals['transactions'] ?? 0),
                'promedio_venta' => $this->sanitizeFloat($totals['average_sale'] ?? 0),
                'ultimas_ventas' => $this->normalizeRecentSales($recent),
                'ventas_semana' => $this->sanitizeFloat($ventasSemana),
                'ventas_ultimos_7_dias' => $this->normalizeDailySeries($ventasUltimos7Dias),
                'metodos_pago' => $this->normalizePaymentDistribution($metodosPago),
            ];
            
            // Debug: Log response
            error_log("Dashboard Response: " . print_r($response, true));
            
            $this->sendJson($response);
        } catch (\Throwable $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            error_log("Stack Trace: " . $e->getTraceAsString());
            $this->sendJson([
                'error' => 'Error al cargar datos del dashboard',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Retorna productos en formato JSON
     */
    public function productos() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        try {
            $productService = new \App\Services\ProductService();
            $productos = $productService->getAllWithVariants();

            // Calcular popularidad (ventas por producto) últimos 30 días
            $saleRepo = new \App\Repositories\SaleRepository();
            $popularity = $saleRepo->getProductPopularity(30); // [producto_id => unidades]
            
            // Transformar objetos Product a formato esperado por el frontend
            // Construir arreglo incluyendo ventas y ranking
            // Primero, recolectar conteos y luego computar ranking
            // Prepare cost service to compute production cost where available
            $costoService = new \App\Services\CostoService(\App\Database\Connection::get());

            $productosArray = array_map(function($producto) use ($popularity, $costoService) {
                $salesCount = $popularity[$producto->id] ?? 0;
                $costo = null;
                try {
                    $c = $costoService->costoProductoNeto($producto->id);
                    $costo = is_numeric($c) ? round((float)$c, 2) : null;
                } catch (\Throwable $e) {
                    // No receta or error calculating cost -> leave null
                    $costo = null;
                }

                return [
                    'producto_id' => $producto->id,
                    'nombre_producto' => $producto->name,
                    'descripcion' => $producto->description,
                    'categoria_id' => $producto->categoryId,
                    'nombre_categoria' => $producto->categoryName,
                    'url_imagen' => $producto->imageUrl,
                    'has_image' => (bool)($producto->hasImage ?? false),
                    'image_url' => ($producto->hasImage ?? false) ? ('/api/productos/' . $producto->id . '/imagen') : null,
                    'activo' => $producto->active,
                    'costo_produccion' => $costo,
                    'sales_count' => $salesCount,
                    'variantes' => array_map(function($variante) {
                        return [
                            'variante_id' => $variante->id,
                            'producto_id' => $variante->productId,
                            'nombre_variante' => $variante->name,
                            'precio' => $variante->price,
                            'volumen_onzas' => $variante->volumeOunces,
                            'activo' => $variante->active
                        ];
                    }, $producto->variants)
                ];
            }, $productos);

            // Calcular ranking por ventas (mayor a menor)
            usort($productosArray, function($a, $b) {
                $cmp = ($b['sales_count'] ?? 0) <=> ($a['sales_count'] ?? 0);
                // En caso de empate, ordenar por nombre
                return $cmp !== 0 ? $cmp : strcasecmp($a['nombre_producto'] ?? '', $b['nombre_producto'] ?? '');
            });
            
            echo json_encode($productosArray);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar productos']);
        }
    }
    
    /**
     * Retorna categorías en formato JSON
     */
    public function categorias() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        try {
            $categoryService = new \App\Services\CategoryService();
            $categorias = $categoryService->list();
            
            // Transformar objetos Category a formato esperado por el frontend
            $categoriasArray = array_map(function($categoria) {
                return [
                    'categoria_id' => $categoria->id,
                    'nombre_categoria' => $categoria->name
                ];
            }, $categorias);
            
            echo json_encode($categoriasArray);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar categorías']);
        }
    }
    
    /**
     * Retorna métodos de pago en formato JSON
     */
    public function metodosPago() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        try {
            $paymentService = new \App\Services\PaymentMethodService();
            $metodos = $paymentService->list();
            
            // Transformar objetos PaymentMethod a formato esperado por el frontend
            $metodosArray = array_map(function($metodo) {
                return [
                    'metodo_id' => $metodo->id,
                    'nombre_metodo' => $metodo->name
                ];
            }, $metodos);
            
            echo json_encode($metodosArray);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar métodos de pago']);
        }
    }

    /**
     * Devuelve la imagen BLOB de un producto
     */
    public function productoImagen($id)
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->sendJson(['error' => 'No autenticado'], 401);
            exit;
        }

        $productId = (int)$id;
        if ($productId <= 0) {
            $this->sendJson(['error' => 'ID inválido'], 400);
            return;
        }

        try {
            $repo = new \App\Repositories\ProductRepository();
            $img = $repo->getImageBlob($productId);
            if (!$img) {
                $this->sendJson(['error' => 'Imagen no encontrada'], 404);
                return;
            }

            header('Content-Type: ' . $img['mime']);
            header('Cache-Control: private, max-age=86400');
            echo $img['data'];
        } catch (\Exception $e) {
            $this->sendJson(['error' => 'Error al obtener imagen'], 500);
        }
    }

    /**
     * Sube/actualiza la imagen BLOB de un producto (multipart/form-data)
     */
    public function productoImagenUpload($id)
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->sendJson(['error' => 'No autenticado'], 401);
            exit;
        }

        // Rol mínimo admin para subir imágenes
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $this->sendJson(['error' => 'No autorizado'], 403);
            exit;
        }

        $productId = (int)$id;
        if ($productId <= 0) {
            $this->sendJson(['error' => 'ID inválido'], 400);
            return;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'El archivo es demasiado grande (límite del servidor)',
                UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande (límite del formulario)',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir en el disco',
                UPLOAD_ERR_EXTENSION => 'Extensión de archivo no permitida'
            ];
            $errorCode = $_FILES['imagen']['error'] ?? UPLOAD_ERR_NO_FILE;
            $errorMsg = $errorMessages[$errorCode] ?? 'Error desconocido al subir archivo';
            $this->sendJson(['error' => $errorMsg], 400);
            return;
        }

        $tmp = $_FILES['imagen']['tmp_name'];
        $name = $_FILES['imagen']['name'] ?? 'imagen';
        $size = (int)($_FILES['imagen']['size'] ?? 0);
        
        // ===== VALIDACIÓN MIME MEJORADA =====
        // 1. Verificar con finfo (más confiable que mime_content_type)
        $detectedMime = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detectedMime = finfo_file($finfo, $tmp);
                finfo_close($finfo);
            }
        }
        
        // 2. Fallback a mime_content_type si finfo no está disponible
        if (!$detectedMime && function_exists('mime_content_type')) {
            $detectedMime = @mime_content_type($tmp) ?: null;
        }
        
        // 3. Si no hay detección, rechazar por seguridad
        if (!$detectedMime) {
            $this->sendJson([
                'error' => 'No se pudo verificar el tipo de archivo. Extensión fileinfo no disponible.',
                'hint' => 'Asegúrese de que la extensión fileinfo esté habilitada en PHP.'
            ], 500);
            return;
        }
        
        // 4. Usar el MIME detectado (no confiar en el enviado por el cliente)
        $mime = $detectedMime;
        // ===== FIN VALIDACIÓN MIME =====

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $max = 25 * 1024 * 1024; // 25MB para archivo original (se procesa a máximo 5MB)
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        // Validar que el MIME detectado esté en la lista permitida
        if (!in_array($mime, $allowed, true)) {
            $this->sendJson([
                'error' => 'Tipo de archivo no permitido',
                'detected_mime' => $mime,
                'allowed_types' => $allowed,
                'hint' => 'Solo se permiten imágenes JPEG, PNG y WebP'
            ], 400);
            return;
        }
        
        // Validar extensión y tamaño
        if (!in_array($ext, $allowedExt, true) || $size <= 0 || $size > $max) {
            $this->sendJson([
                'error' => 'Extensión o tamaño no válido',
                'file' => htmlspecialchars($name),
                'detected_mime' => $mime,
                'file_size_mb' => round($size / 1024 / 1024, 2),
                'allowed_ext' => $allowedExt,
                'max_size_mb' => round($max / 1024 / 1024, 1)
            ], 400);
            return;
        }

        $originalData = file_get_contents($tmp);
        if ($originalData === false) {
            $this->sendJson(['error' => 'No se pudo leer el archivo temporal'], 500);
            return;
        }

        try {
            $repo = new \App\Repositories\ProductRepository();
            
            // Verificar que el producto existe
            $product = $repo->findById($productId);
            if (!$product) {
                $this->sendJson(['error' => 'Producto no encontrado'], 404);
                return;
            }
            
            // Procesar imagen: redimensionar y comprimir
            $imageProcessor = new \App\Services\ImageProcessingService();
            
            if (!$imageProcessor->canProcess($mime)) {
                $this->sendJson(['error' => 'Tipo de imagen no soportado para procesamiento: ' . $mime], 400);
                return;
            }
            
            $processedImage = $imageProcessor->processImage($originalData, $mime, $name);
            
            // Información sobre el procesamiento
            $originalSize = strlen($originalData);
            $processedSize = $processedImage['size'];
            $compressionRatio = round((1 - $processedSize / $originalSize) * 100, 1);
            
            // Guardar imagen procesada
            $ok = $repo->updateImage(
                $productId, 
                $processedImage['data'], 
                $processedImage['mime'], 
                $processedImage['name'], 
                $processedImage['size']
            );
            
            if (!$ok) {
                throw new \RuntimeException('No se pudo guardar en la base de datos');
            }
            
            $this->sendJson([
                'ok' => true,
                'message' => 'Imagen subida correctamente',
                'processing_info' => [
                    'original_size_kb' => round($originalSize / 1024, 1),
                    'processed_size_kb' => round($processedImage['size'] / 1024, 1),
                    'processed_name' => $processedImage['name'],
                    'saved_space' => $originalSize > $processedImage['size'] ? 
                        round(($originalSize - $processedImage['size']) / 1024, 1) . ' KB ahorrados' : 
                        'Sin compresión adicional'
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en productoImagenUpload: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->sendJson(['error' => 'Error al subir imagen: ' . $e->getMessage()], 500);
        }
    }

    private function sendJson(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, must-revalidate');
            header('Pragma: no-cache');
        }

        http_response_code($statusCode);

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            http_response_code(500);
            echo '{"error":"Error al serializar la respuesta"}';
            return;
        }

        echo $json;
    }

    private function sanitizeFloat($value): float
    {
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        if (!is_numeric($value)) {
            return 0.0;
        }

        return round((float) $value, 2);
    }

    private function sanitizeInt($value): int
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return (int) $value;
    }

    private function sanitizeString($value, int $maxLength = 120): string
    {
        if (is_null($value)) {
            return '';
        }

        if (!is_string($value)) {
            $value = is_numeric($value) ? (string) $value : '';
        }

        if ($value === '') {
            return '';
        }

        $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? '';
        $trimmed = trim($clean);

        if ($trimmed === '') {
            return '';
        }

        if (function_exists('mb_substr')) {
            return mb_substr($trimmed, 0, $maxLength, 'UTF-8');
        }

        return substr($trimmed, 0, $maxLength);
    }

    private function normalizeRecentSales(array $recent): array
    {
        $normalized = [];

        foreach ($recent as $sale) {
            $codigo = $this->sanitizeString($this->extractValue($sale, 'code'), 50);
            $hora = $this->sanitizeString($this->extractValue($sale, 'time'), 16);
            $metodo = $this->sanitizeString($this->extractValue($sale, 'paymentMethod'), 60);
            $usuario = $this->sanitizeString($this->extractValue($sale, 'user'), 120);
            $total = $this->sanitizeFloat($this->extractValue($sale, 'total'));

            $normalized[] = [
                'codigo' => $codigo !== '' ? $codigo : 'N/D',
                'hora' => $hora !== '' ? $hora : '--:--',
                'metodo_pago' => $metodo !== '' ? $metodo : 'No definido',
                'usuario' => $usuario !== '' ? $usuario : 'Usuario',
                'total' => $total,
            ];
        }

        return $normalized;
    }

    private function normalizeDailySeries($series): array
    {
        if (!is_array($series)) {
            return [];
        }

        $normalized = [];

        foreach ($series as $point) {
            $label = $this->sanitizeString($this->extractValue($point, 'fecha'), 24);
            $total = $this->sanitizeFloat($this->extractValue($point, 'total'));

            $normalized[] = [
                'fecha' => $label !== '' ? $label : '--',
                'total' => $total,
            ];
        }

        return $normalized;
    }

    private function normalizePaymentDistribution($entries): array
    {
        if (!is_array($entries)) {
            return [];
        }

        $normalized = [];

        foreach ($entries as $entry) {
            $metodo = $this->sanitizeString($this->extractValue($entry, 'metodo'), 60);
            if ($metodo === '') {
                $metodo = $this->sanitizeString($this->extractValue($entry, 'method'), 60);
            }

            $total = $this->sanitizeFloat($this->extractValue($entry, 'total'));

            $normalized[] = [
                'metodo' => $metodo !== '' ? $metodo : 'Otros',
                'total' => $total,
            ];
        }

        return $normalized;
    }

    private function extractValue($source, string $property)
    {
        if (is_array($source) && array_key_exists($property, $source)) {
            return $source[$property];
        }

        if (is_object($source) && isset($source->{$property})) {
            return $source->{$property};
        }

        return null;
    }
}
