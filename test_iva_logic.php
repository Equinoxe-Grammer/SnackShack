<?php
/**
 * Prueba de verificación de lógica de IVA
 * Este script simula el cálculo de IVA en frontend y backend
 */

echo "=== ANÁLISIS COMPLETO DE LÓGICA DE IVA ===\n\n";

// Escenario de prueba: 2 productos en el carrito
$cart = [
    [
        'nombre' => 'Frappe Chocolate Grande',
        'precio_unitario' => 100.00,
        'cantidad' => 1
    ],
    [
        'nombre' => 'Frappe Vainilla Mediano',
        'precio_unitario' => 80.00,
        'cantidad' => 2
    ]
];

echo "CARRITO:\n";
echo "--------\n";
foreach ($cart as $idx => $item) {
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   Precio unitario: $" . number_format($item['precio_unitario'], 2) . "\n";
    echo "   Cantidad: {$item['cantidad']}\n";
    echo "   Subtotal: $" . number_format($item['precio_unitario'] * $item['cantidad'], 2) . "\n\n";
}

echo "\n=== CÁLCULOS FRONTEND (vista.php) ===\n\n";

// Simular cálculo por item (lo que se muestra en cada producto)
echo "IVA POR PRODUCTO (mostrado en cada item):\n";
echo "-----------------------------------------\n";
$totalIvaItems = 0;
foreach ($cart as $idx => $item) {
    $itemSubtotal = $item['precio_unitario'] / 1.15;
    $itemIva = $itemSubtotal * 0.15;
    $ivaTotal = $itemIva * $item['cantidad'];
    $totalIvaItems += $ivaTotal;
    
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   Precio unitario: $" . number_format($item['precio_unitario'], 2) . "\n";
    echo "   Base sin IVA: $" . number_format($itemSubtotal, 2) . "\n";
    echo "   IVA unitario: $" . number_format($itemIva, 2) . "\n";
    echo "   IVA total (x{$item['cantidad']}): $" . number_format($ivaTotal, 2) . "\n";
    echo "   Verificación: $" . number_format($itemSubtotal, 2) . " + $" . number_format($itemIva, 2) . " = $" . number_format($item['precio_unitario'], 2) . "\n\n";
}

// Simular cálculo del resumen (lo que se muestra en el footer)
echo "RESUMEN DEL CARRITO:\n";
echo "--------------------\n";
$total = 0;
foreach ($cart as $item) {
    $total += $item['precio_unitario'] * $item['cantidad'];
}

$subtotal = $total / 1.15;
$iva = $subtotal * 0.15;

echo "Subtotal (sin IVA): $" . number_format($subtotal, 2) . "\n";
echo "IVA (15%): $" . number_format($iva, 2) . "\n";
echo "Total: $" . number_format($total, 2) . "\n\n";

echo "VERIFICACIÓN:\n";
echo "-------------\n";
echo "Suma de IVAs individuales: $" . number_format($totalIvaItems, 2) . "\n";
echo "IVA del resumen: $" . number_format($iva, 2) . "\n";
echo "¿Coinciden? " . (abs($totalIvaItems - $iva) < 0.01 ? "✓ SÍ" : "✗ NO - DIFERENCIA: $" . number_format(abs($totalIvaItems - $iva), 2)) . "\n\n";

echo "\n=== CÁLCULOS BACKEND (SaleService.php) ===\n\n";

// Simular backend
$backendTotal = 0.0;
$backendItems = [];

foreach ($cart as $item) {
    $unitPrice = $item['precio_unitario'];
    $quantity = $item['cantidad'];
    $subtotal = round($unitPrice * $quantity, 2);
    
    // Cálculo backend
    $unitNet = round($unitPrice / 1.15, 4);
    $unitIva = round($unitPrice - $unitNet, 4);
    $lineIvaTotal = round($unitIva * $quantity, 2);
    
    $backendItems[] = [
        'nombre' => $item['nombre'],
        'unitPrice' => $unitPrice,
        'quantity' => $quantity,
        'subtotal' => $subtotal,
        'unitNet' => $unitNet,
        'unitIva' => $unitIva,
        'lineIvaTotal' => $lineIvaTotal
    ];
    
    $backendTotal += $subtotal;
}

$backendTotal = round($backendTotal, 2);

echo "ITEMS PROCESADOS EN BACKEND:\n";
echo "----------------------------\n";
$totalIvaBackend = 0;
foreach ($backendItems as $idx => $item) {
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   Precio unitario: $" . number_format($item['unitPrice'], 2) . "\n";
    echo "   Base unitaria: $" . number_format($item['unitNet'], 4) . "\n";
    echo "   IVA unitario: $" . number_format($item['unitIva'], 4) . "\n";
    echo "   IVA línea (x{$item['quantity']}): $" . number_format($item['lineIvaTotal'], 2) . "\n";
    echo "   Subtotal: $" . number_format($item['subtotal'], 2) . "\n\n";
    $totalIvaBackend += $item['lineIvaTotal'];
}

echo "TOTAL BACKEND: $" . number_format($backendTotal, 2) . "\n";
echo "TOTAL IVA BACKEND: $" . number_format($totalIvaBackend, 2) . "\n\n";

echo "\n=== COMPARACIÓN FRONTEND vs BACKEND ===\n\n";

echo "Total Frontend: $" . number_format($total, 2) . "\n";
echo "Total Backend:  $" . number_format($backendTotal, 2) . "\n";
echo "¿Coinciden? " . (abs($total - $backendTotal) < 0.01 ? "✓ SÍ" : "✗ NO") . "\n\n";

echo "IVA Frontend (resumen): $" . number_format($iva, 2) . "\n";
echo "IVA Backend (suma):     $" . number_format($totalIvaBackend, 2) . "\n";
echo "¿Coinciden? " . (abs($iva - $totalIvaBackend) < 0.01 ? "✓ SÍ" : "✗ NO") . "\n\n";

echo "IVA Frontend (items):   $" . number_format($totalIvaItems, 2) . "\n";
echo "IVA Backend (suma):     $" . number_format($totalIvaBackend, 2) . "\n";
echo "¿Coinciden? " . (abs($totalIvaItems - $totalIvaBackend) < 0.01 ? "✓ SÍ" : "✗ NO") . "\n\n";

echo "\n=== ANÁLISIS DE INCONSISTENCIAS ===\n\n";

// Detectar problema
$problema = false;

echo "1. CÁLCULO BACKEND (ACTUAL):\n";
echo "   unitNet = precio / 1.15\n";
echo "   unitIva = precio - unitNet\n";
echo "   Ejemplo: $100 / 1.15 = $86.9565, $100 - $86.9565 = $13.0435\n\n";

echo "2. CÁLCULO FRONTEND (ACTUAL):\n";
echo "   itemSubtotal = precio / 1.15\n";
echo "   itemIva = itemSubtotal * 0.15\n";
echo "   Ejemplo: $100 / 1.15 = $86.96, $86.96 * 0.15 = $13.044\n\n";

echo "3. PROBLEMA POTENCIAL:\n";
$testPrice = 100.00;
$backendNet = round($testPrice / 1.15, 4);
$backendIva = round($testPrice - $backendNet, 4);
$frontendNet = $testPrice / 1.15;
$frontendIva = $frontendNet * 0.15;

echo "   Precio de prueba: $" . number_format($testPrice, 2) . "\n";
echo "   Backend IVA: $" . number_format($backendIva, 4) . " (precio - neto)\n";
echo "   Frontend IVA: $" . number_format($frontendIva, 4) . " (neto * 0.15)\n";
echo "   Diferencia: $" . number_format(abs($backendIva - $frontendIva), 4) . "\n\n";

if (abs($backendIva - $frontendIva) > 0.001) {
    echo "   ⚠️ INCONSISTENCIA DETECTADA\n";
    $problema = true;
} else {
    echo "   ✓ Sin inconsistencia significativa\n";
}

echo "\n=== RECOMENDACIONES ===\n\n";

if ($problema) {
    echo "❌ PROBLEMA ENCONTRADO:\n";
    echo "   Backend y Frontend usan fórmulas diferentes para calcular IVA.\n\n";
    echo "SOLUCIÓN RECOMENDADA:\n";
    echo "   Unificar ambos cálculos usando: itemIva = itemSubtotal * 0.15\n";
    echo "   Esto es más claro y evita errores de redondeo.\n\n";
    echo "CAMBIO EN BACKEND (SaleService.php línea 125-127):\n";
    echo "   // ANTES:\n";
    echo "   \$unitNet = round(\$unitPrice / 1.15, 4);\n";
    echo "   \$unitIva = round(\$unitPrice - \$unitNet, 4);\n\n";
    echo "   // DESPUÉS:\n";
    echo "   \$unitNet = round(\$unitPrice / 1.15, 4);\n";
    echo "   \$unitIva = round(\$unitNet * 0.15, 4);\n";
} else {
    echo "✓ La lógica es consistente entre frontend y backend.\n";
    echo "  Los cálculos coinciden correctamente.\n";
}

echo "\n=== FIN DEL ANÁLISIS ===\n";
