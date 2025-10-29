<?php
namespace App\Services;

/**
 * Servicio para procesar imágenes: validar y limitar tamaño
 * Versión simplificada sin GD para compatibilidad
 */
class ImageProcessingService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB máximo
    private const MAX_DIMENSION  = 1920; // máx ancho/alto al procesar
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Procesa una imagen: valida y limita tamaño
     * 
     * @param string $imageData Datos binarios de la imagen original
     * @param string $mimeType Tipo MIME de la imagen
     * @param string $originalName Nombre original del archivo
     * @return array ['data' => string, 'mime' => string, 'size' => int, 'name' => string]
     * @throws \Exception Si no se puede procesar la imagen
     */
    public function processImage(string $imageData, string $mimeType, string $originalName): array
    {
        $originalSize = strlen($imageData);
        
        // Validar tipo MIME
        if (!$this->canProcess($mimeType)) {
            throw new \Exception("Tipo de imagen no soportado: {$mimeType}");
        }
        
        // Si la imagen es demasiado grande, rechazarla
        if ($originalSize > self::MAX_FILE_SIZE) {
            $maxMB = round(self::MAX_FILE_SIZE / 1024 / 1024, 1);
            $currentMB = round($originalSize / 1024 / 1024, 1);
            throw new \Exception("Imagen demasiado grande ({$currentMB}MB). Máximo permitido: {$maxMB}MB");
        }
        
        // Si hay librerías de imagen disponibles, intentar comprimir/redimensionar si supera límites razonables
        if (extension_loaded('gd')) {
            $result = $this->processWithGd($imageData, $mimeType);
            return [
                'data' => $result['data'],
                'mime' => $result['mime'],
                'size' => strlen($result['data']),
                'name' => $this->generateProcessedName($originalName)
            ];
        }

        // Sin librerías, devolver tal cual
        return [
            'data' => $imageData,
            'mime' => $mimeType,
            'size' => $originalSize,
            'name' => $this->generateProcessedName($originalName)
        ];
    }

    /**
     * Valida si se puede procesar el tipo de imagen
     */
    public function canProcess(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_TYPES);
    }

    /**
     * Genera nombre procesado para la imagen
     */
    private function generateProcessedName(string $originalName): string
    {
        $info = pathinfo($originalName);
        $baseName = $info['filename'] ?? 'imagen';
        $extension = strtolower($info['extension'] ?? 'jpg');
        
        // Normalizar extensión
        if (in_array($extension, ['jpeg', 'jpg'])) {
            $extension = 'jpg';
        } elseif ($extension === 'png') {
            $extension = 'png';
        } elseif ($extension === 'webp') {
            $extension = 'webp';
        } else {
            $extension = 'jpg'; // Por defecto JPEG
        }
        
        return $baseName . '_uploaded.' . $extension;
    }

    /**
     * Obtiene información sobre los límites de procesamiento
     */
    public function getProcessingLimits(): array
    {
        return [
            'max_file_size' => self::MAX_FILE_SIZE,
            'max_file_size_mb' => round(self::MAX_FILE_SIZE / 1024 / 1024, 1),
            'allowed_types' => self::ALLOWED_TYPES,
            'has_gd' => extension_loaded('gd'),
            'has_imagick' => extension_loaded('imagick'),
            'max_dimension' => self::MAX_DIMENSION
        ];
    }

    /**
     * Verifica las dimensiones de una imagen si es posible
     */
    public function getImageInfo(string $imageData): ?array
    {
        $info = @getimagesizefromstring($imageData);
        if ($info === false) {
            return null;
        }
        
        return [
            'width' => $info[0],
            'height' => $info[1],
            'mime' => $info['mime'] ?? null,
            'bits' => $info['bits'] ?? null,
            'channels' => $info['channels'] ?? null
        ];
    }

    /**
     * Sugiere el tamaño máximo recomendado para subir
     */
    public function getRecommendedUploadSize(): array
    {
        return [
            'max_dimension' => self::MAX_DIMENSION, // pixels
            'max_file_size_mb' => round(self::MAX_FILE_SIZE / 1024 / 1024, 1),
            'recommended_formats' => ['JPEG para fotografías', 'PNG para gráficos', 'WebP para mejor compresión'],
            'tips' => [
                'Redimensiona tu imagen a máximo 1920x1080 pixels antes de subir',
                'Usa calidad JPEG 80-90% para un buen balance tamaño/calidad',
                'Evita subir imágenes directamente desde la cámara sin procesar'
            ]
        ];
    }

    // --- Helpers de procesamiento ---
    private function processWithGd(string $imageData, string $mimeType): array
    {
        $src = @imagecreatefromstring($imageData);
        if (!$src) {
            // Si GD falla, devolver original
            return ['data' => $imageData, 'mime' => $mimeType];
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = 1.0;
        if ($w > self::MAX_DIMENSION || $h > self::MAX_DIMENSION) {
            $scale = min(self::MAX_DIMENSION / $w, self::MAX_DIMENSION / $h);
        }
        $nw = (int)floor($w * $scale);
        $nh = (int)floor($h * $scale);

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $format = $this->mapFormat($mimeType);
        ob_start();
        if ($format === 'jpeg') {
            imagejpeg($dst, null, 85);
        } elseif ($format === 'png') {
            imagepng($dst, null, 6);
        } elseif ($format === 'webp' && function_exists('imagewebp')) {
            imagewebp($dst, null, 85);
        } else {
            imagejpeg($dst, null, 85);
        }
        $out = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        if ($out === false) {
            return ['data' => $imageData, 'mime' => $mimeType];
        }
        return ['data' => $out, 'mime' => $this->mimeFromFormat($format)];
    }

    private function mapFormat(string $mime): string
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpeg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpeg',
        };
    }

    private function mimeFromFormat(string $format): string
    {
        return match ($format) {
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}