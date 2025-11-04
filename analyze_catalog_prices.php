<?php
require 'bootstrap.php';

use App\Database\Connection;

echo "=== ANÁLISIS DE PRECIOS EN CATÁLOGO ===\n\n";

try {
    $db = Connection::get();
    
    // Obtener todos los productos con sus variantes
    $stmt = $db->query("
        SELECT 
            p.producto_id,
            p.nombre_producto,
            p.activo as producto_activo,
            v.variante_id,
            v.nombre_variante,
            v.precio,
            v.activo as variante_activa,
            c.nombre_categoria
        FROM variantes v
        JOIN productos p ON v.producto_id = p.producto_id
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        ORDER BY p.nombre_producto, v.precio
    ");
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($productos)) {
        echo "⚠️ NO HAY PRODUCTOS EN LA BASE DE DATOS\n\n";
        echo "Necesitas agregar productos al catálogo.\n";
        exit;
    }
    
    echo "PRODUCTOS Y PRECIOS ACTUALES:\n";
    echo str_repeat("=", 100) . "\n";
    printf("%-30s %-15s %-12s %-12s %-15s %s\n", 
        "PRODUCTO", "VARIANTE", "PRECIO", "IVA INCL.", "CATEGORÍA", "ESTADO");
    echo str_repeat("-", 100) . "\n";
    
    $totalProductos = 0;
    $productosConPrecio = 0;
    $sumaPrecios = 0;
    $sumaIVA = 0;
    
    foreach ($productos as $prod) {
        $precio = (float) $prod['precio'];
        $base = $precio / 1.15;
        $iva = $base * 0.15;
        
        $estado = ($prod['producto_activo'] && $prod['variante_activa']) ? '✓ Activo' : '✗ Inactivo';
        
        printf("%-30s %-15s $%10.2f $%10.2f %-15s %s\n",
            substr($prod['nombre_producto'], 0, 29),
            substr($prod['nombre_variante'], 0, 14),
            $precio,
            $iva,
            substr($prod['nombre_categoria'] ?? 'Sin cat.', 0, 14),
            $estado
        );
        
        $totalProductos++;
        if ($precio > 0) {
            $productosConPrecio++;
            $sumaPrecios += $precio;
            $sumaIVA += $iva;
        }
    }
    
    echo str_repeat("=", 100) . "\n\n";
    
    echo "ESTADÍSTICAS:\n";
    echo str_repeat("-", 50) . "\n";
    echo "Total de variantes: $totalProductos\n";
    echo "Variantes con precio: $productosConPrecio\n";
    echo "Variantes sin precio: " . ($totalProductos - $productosConPrecio) . "\n";
    
    if ($productosConPrecio > 0) {
        $promedioPrecio = $sumaPrecios / $productosConPrecio;
        $promedioIVA = $sumaIVA / $productosConPrecio;
        
        echo "\nPROMEDIOS:\n";
        echo "Precio promedio: $" . number_format($promedioPrecio, 2) . "\n";
        echo "IVA promedio incluido: $" . number_format($promedioIVA, 2) . "\n";
        echo "% IVA del total: " . number_format(($sumaIVA / $sumaPrecios) * 100, 2) . "%\n";
    }
    
    echo "\n" . str_repeat("=", 100) . "\n\n";
    
    echo "ANÁLISIS DE PRECIOS:\n";
    echo str_repeat("-", 50) . "\n";
    
    // Verificar si hay precios sin IVA incluido (sospechosos)
    $preciosSospechosos = [];
    foreach ($productos as $prod) {
        $precio = (float) $prod['precio'];
        if ($precio > 0) {
            $base = $precio / 1.15;
            $iva = $base * 0.15;
            $porcentajeIVA = ($iva / $precio) * 100;
            
            // El IVA incluido debe ser aproximadamente 13.04% del total
            if (abs($porcentajeIVA - 13.04) > 1) {
                $preciosSospechosos[] = [
                    'producto' => $prod['nombre_producto'],
                    'variante' => $prod['nombre_variante'],
                    'precio' => $precio,
                    'porcentaje' => $porcentajeIVA
                ];
            }
        }
    }
    
    if (empty($preciosSospechosos)) {
        echo "✅ TODOS LOS PRECIOS PARECEN INCLUIR IVA CORRECTAMENTE\n";
        echo "   El IVA representa aproximadamente 13.04% del precio total en todos los productos.\n";
    } else {
        echo "⚠️ PRECIOS SOSPECHOSOS (IVA no parece estar incluido correctamente):\n\n";
        foreach ($preciosSospechosos as $sospechoso) {
            printf("   - %s (%s): $%.2f (IVA: %.2f%%)\n",
                $sospechoso['producto'],
                $sospechoso['variante'],
                $sospechoso['precio'],
                $sospechoso['porcentaje']
            );
        }
    }
    
    echo "\n" . str_repeat("=", 100) . "\n\n";
    
    echo "RECOMENDACIONES:\n";
    echo str_repeat("-", 50) . "\n";
    echo "1. Los precios en el catálogo deben INCLUIR el IVA del 15%\n";
    echo "2. Para calcular precio con IVA: Precio_Base × 1.15\n";
    echo "3. Para calcular precio base: Precio_Total / 1.15\n";
    echo "4. El IVA incluido debe ser: (Precio_Base × 0.15)\n\n";
    
    echo "EJEMPLO:\n";
    echo "  Si quieres vender a $100 (precio final al cliente):\n";
    echo "  - Guarda en DB: $100.00\n";
    echo "  - Base imponible: $100 / 1.15 = $86.96\n";
    echo "  - IVA: $86.96 × 0.15 = $13.04\n";
    echo "  - Total: $86.96 + $13.04 = $100.00 ✓\n\n";
    
    echo "  Si tu costo es $50 y quieres 100% de margen:\n";
    echo "  - Precio base: $50 × 2 = $100\n";
    echo "  - Precio con IVA: $100 × 1.15 = $115.00\n";
    echo "  - Guarda en DB: $115.00\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL ANÁLISIS ===\n";
