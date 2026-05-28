<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinero Antes — Agente IA de Consultoría</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .gradient-text {
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .blue-accent {
            background-color: #3b82f6;
        }
        .light-blue-bg {
            background-color: #eff6ff;
        }
        .feature-card {
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
        }
        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
        .progress-bar {
            transition: width 1s ease-in-out;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .confetti {
            position: fixed;
            pointer-events: none;
            z-index: 50;
        }
    </style>
</head>
<body>
<?php
// ──────────────────────────────────────────────
// Motor de análisis (portado de dinero_antes.py)
// ──────────────────────────────────────────────

function analizar(array $datos): array {
    $c = $datos['costo'];
    $p = $datos['precio'];
    $q = $datos['ventas_mensuales'];
    $cf = $datos['costos_fijos'];
    $inv = $datos['inversion'];
    $comp = $datos['competidores'];
    $mercado = $datos['tamano_mercado'];

    $margen_unitario = $p - $c;
    $margen_porcentual = ($p > 0) ? ($margen_unitario / $p * 100) : 0;
    $contribucion_mensual = $margen_unitario * $q;
    $ganancia_neta_mensual = $contribucion_mensual - $cf;
    $margen_neto_porcentual = ($p * $q > 0) ? ($ganancia_neta_mensual / ($p * $q) * 100) : 0;

    $punto_eq_unidades = ($margen_unitario > 0) ? ceil($cf / $margen_unitario) : INF;
    $participacion_necesaria = ($mercado > 0) ? ($q / $mercado * 100) : 0;

    if ($ganancia_neta_mensual > 0) {
        $meses_recupero = ceil($inv / $ganancia_neta_mensual);
        $recupera_en_12m = ($ganancia_neta_mensual * 12 >= $inv);
    } else {
        $meses_recupero = INF;
        $recupera_en_12m = false;
    }

    $score = 0;
    $razones = [];

    if ($margen_porcentual >= 40) {
        $score += 25;
    } elseif ($margen_porcentual >= 20) {
        $score += 15;
    } elseif ($margen_porcentual > 0) {
        $score += 5;
    } else {
        $razones[] = "Margen negativo: vendés a pérdida por unidad.";
    }

    if ($ganancia_neta_mensual > 0) {
        if ($ganancia_neta_mensual > $cf) {
            $score += 30;
        } elseif ($ganancia_neta_mensual > $cf * 0.5) {
            $score += 20;
        } else {
            $score += 10;
        }
    } else {
        $razones[] = "Ganancia neta mensual negativa o nula.";
    }

    if ($meses_recupero <= 6) {
        $score += 20;
    } elseif ($meses_recupero <= 12) {
        $score += 15;
    } elseif ($meses_recupero <= 24) {
        $score += 5;
    } else {
        $razones[] = "La inversión no se recupera en menos de 2 años.";
    }

    if ($comp <= 3) {
        $score += 10;
    } elseif ($comp <= 10) {
        $score += 5;
    } else {
        $razones[] = "Mercado muy competitivo.";
    }

    if ($participacion_necesaria <= 1) {
        $score += 15;
    } elseif ($participacion_necesaria <= 5) {
        $score += 10;
    } elseif ($participacion_necesaria <= 15) {
        $score += 5;
    } else {
        $razones[] = "Necesitás una porción de mercado muy alta.";
    }

    if ($score >= 80) {
        $decision = "SÍ GENERA DINERO";
        $confianza = "ALTA";
    } elseif ($score >= 60) {
        $decision = "POSIBLE — pero con riesgos";
        $confianza = "MEDIA";
    } else {
        $decision = "NO GENERA DINERO";
        $confianza = "BAJA";
    }

    return [
        'margen_unitario' => $margen_unitario,
        'margen_porcentual' => round($margen_porcentual, 1),
        'contribucion_mensual' => $contribucion_mensual,
        'ganancia_neta_mensual' => $ganancia_neta_mensual,
        'margen_neto_porcentual' => round($margen_neto_porcentual, 1),
        'punto_eq_unidades' => $punto_eq_unidades,
        'participacion_necesaria' => round($participacion_necesaria, 1),
        'meses_recupero' => $meses_recupero,
        'recupera_en_12m' => $recupera_en_12m,
        'score' => round($score, 1),
        'decision' => $decision,
        'confianza' => $confianza,
        'razones' => $razones,
    ];
}

$resultado = null;
$datos = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analizar'])) {
    $datos = [
        'nombre' => $_POST['nombre'] ?? 'Mi producto',
        'costo' => floatval($_POST['costo'] ?? 0),
        'precio' => floatval($_POST['precio'] ?? 0),
        'ventas_mensuales' => intval($_POST['ventas_mensuales'] ?? 0),
        'costos_fijos' => floatval($_POST['costos_fijos'] ?? 0),
        'inversion' => floatval($_POST['inversion'] ?? 0),
        'competidores' => intval($_POST['competidores'] ?? 0),
        'tamano_mercado' => intval($_POST['tamano_mercado'] ?? 1000),
    ];
    $resultado = analizar($datos);
}
?>

    <div class="min-h-screen py-8 px-4">
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-8 fade-in">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 rounded-full blue-accent flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h1 class="text-5xl font-bold gradient-text ml-4">Dinero Antes</h1>
                </div>
                <p class="text-xl text-gray-600">Agente IA — ¿Tu producto genera dinero antes de gastar un peso?</p>
            </div>

            <?php if ($resultado): ?>

            <!-- Resultados -->
            <div class="fade-in space-y-6 mb-8">

                <!-- Score y Decisión -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-8 text-center" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full blue-accent shadow-lg mb-4">
                            <span class="text-3xl font-bold text-white"><?= $resultado['score'] ?></span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Puntuación: <?= $resultado['score'] ?>/100</h2>
                        <div class="w-full max-w-md mx-auto bg-gray-200 rounded-full h-4 mb-4">
                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-4 rounded-full progress-bar" style="width: <?= $resultado['score'] ?>%"></div>
                        </div>
                        <div class="text-2xl font-bold mb-2">
                            <?php if ($resultado['score'] >= 80): ?>
                                <span class="text-green-600">✅ <?= $resultado['decision'] ?></span>
                            <?php elseif ($resultado['score'] >= 60): ?>
                                <span class="text-yellow-600">⚠️ <?= $resultado['decision'] ?></span>
                            <?php else: ?>
                                <span class="text-red-600">❌ <?= $resultado['decision'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="inline-flex items-center bg-white rounded-full px-4 py-2 shadow-sm">
                            <div class="w-3 h-3 rounded-full bg-blue-600 pulse-dot mr-2"></div>
                            <span class="text-gray-700 font-medium">Confianza: <?= $resultado['confianza'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Métricas clave -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-full blue-accent flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Métricas clave</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Precio venta</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['precio'], 0) ?></div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Costo unitario</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['costo'], 0) ?></div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Margen unitario</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($resultado['margen_unitario'], 0) ?></div>
                            <div class="text-sm text-blue-600 font-medium"><?= $resultado['margen_porcentual'] ?>%</div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Ventas mensuales</div>
                            <div class="text-2xl font-bold text-gray-800"><?= number_format($datos['ventas_mensuales']) ?></div>
                            <div class="text-sm text-gray-500">unidades</div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Contribución mensual</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($resultado['contribucion_mensual'], 0) ?></div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Costos fijos/mes</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['costos_fijos'], 0) ?></div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Ganancia neta/mes</div>
                            <div class="text-2xl font-bold <?= $resultado['ganancia_neta_mensual'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                $<?= number_format($resultado['ganancia_neta_mensual'], 0) ?>
                            </div>
                            <div class="text-sm text-blue-600 font-medium"><?= $resultado['margen_neto_porcentual'] ?>%</div>
                        </div>
                        <div class="light-blue-bg rounded-xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">Inversión inicial</div>
                            <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['inversion'], 0) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Indicadores -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-full blue-accent flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Indicadores</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="feature-card bg-blue-50 rounded-xl p-5">
                            <div class="text-sm text-gray-500 mb-1">Punto de equilibrio</div>
                            <div class="text-xl font-bold text-gray-800">
                                <?= is_infinite($resultado['punto_eq_unidades']) ? 'Nunca (margen negativo)' : number_format($resultado['punto_eq_unidades']) . ' unid./mes' ?>
                            </div>
                        </div>
                        <div class="feature-card bg-blue-50 rounded-xl p-5">
                            <div class="text-sm text-gray-500 mb-1">Recupero de inversión</div>
                            <div class="text-xl font-bold text-gray-800">
                                <?= is_infinite($resultado['meses_recupero']) ? 'No se recupera' : '~' . $resultado['meses_recupero'] . ' meses' ?>
                            </div>
                            <?php if ($resultado['recupera_en_12m']): ?>
                                <div class="text-xs text-green-600 font-medium mt-1">✅ Recuperable en 12 meses</div>
                            <?php endif; ?>
                        </div>
                        <div class="feature-card bg-blue-50 rounded-xl p-5">
                            <div class="text-sm text-gray-500 mb-1">Participación de mercado</div>
                            <div class="text-xl font-bold text-gray-800"><?= $resultado['participacion_necesaria'] ?>%</div>
                            <div class="text-xs text-gray-500 mt-1">de <?= number_format($datos['tamano_mercado']) ?> clientes</div>
                        </div>
                        <div class="feature-card bg-blue-50 rounded-xl p-5">
                            <div class="text-sm text-gray-500 mb-1">Competidores</div>
                            <div class="text-xl font-bold text-gray-800"><?= $datos['competidores'] ?></div>
                            <div class="text-xs text-gray-500 mt-1">directos</div>
                        </div>
                    </div>
                </div>

                <!-- Advertencias -->
                <?php if (!empty($resultado['razones'])): ?>
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Advertencias del agente IA</h3>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($resultado['razones'] as $razon): ?>
                            <div class="flex items-start p-4 bg-red-50 rounded-xl border-l-4 border-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700"><?= htmlspecialchars($razon) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tip -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">💡 Tip del agente IA</h4>
                            <p class="text-blue-100">Si el resultado es negativo, ajustá precio, reducí costos o validá con clientes reales antes de invertir.</p>
                        </div>
                    </div>
                </div>

                <!-- Volver -->
                <div class="text-center">
                    <a href="" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Analizar otro producto
                    </a>
                </div>
            </div>

            <?php else: ?>

            <!-- Formulario -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden fade-in">
                <div class="p-8" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                    <div class="flex items-center">
                        <div class="w-14 h-14 rounded-full blue-accent flex items-center justify-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800">Analizar producto</h2>
                            <p class="text-gray-600 mt-1">Completá los datos y el agente IA evaluará la viabilidad de tu producto.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" class="p-8 space-y-6">
                    <input type="hidden" name="analizar" value="1">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del producto</label>
                        <input type="text" name="nombre" value="Mi producto"
                               class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">💰 Costo de producir/vender una unidad (ARS)</label>
                            <input type="number" name="costo" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">🏷️ Precio de venta por unidad (ARS)</label>
                            <input type="number" name="precio" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">📦 Ventas estimadas por mes (unidades)</label>
                            <input type="number" name="ventas_mensuales" min="0" required
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">🏢 Costos fijos mensuales (ARS)</label>
                            <input type="number" name="costos_fijos" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">💵 Inversión inicial necesaria (ARS)</label>
                            <input type="number" name="inversion" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">👥 ¿Cuántos competidores directos tenés?</label>
                            <input type="number" name="competidores" min="0" value="0"
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">🌍 Tamaño estimado del mercado (clientes potenciales)</label>
                            <input type="number" name="tamano_mercado" min="1" value="1000"
                                   class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-gray-800">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="w-full py-4 blue-accent text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.01]">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Analizar con IA
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 fade-in">
                <div class="bg-white rounded-2xl p-6 shadow-lg feature-card">
                    <div class="flex items-start">
                        <div class="w-12 h-12 rounded-full blue-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Análisis instantáneo</h3>
                            <p class="text-sm text-gray-600 mt-1">El agente IA evalúa tu producto en segundos usando 5 dimensiones clave.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg feature-card">
                    <div class="flex items-start">
                        <div class="w-12 h-12 rounded-full blue-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Score de viabilidad</h3>
                            <p class="text-sm text-gray-600 mt-1">Puntuación de 0 a 100 basada en margen, ganancia, recupero, competencia y mercado.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg feature-card">
                    <div class="flex items-start">
                        <div class="w-12 h-12 rounded-full blue-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Recomendaciones IA</h3>
                            <p class="text-sm text-gray-600 mt-1">Recibí advertencias inteligentes y tips accionables para mejorar tu producto.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            <!-- Footer -->
            <div class="text-center mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center">
                    <div class="w-8 h-8 rounded-full blue-accent flex items-center justify-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500">Dinero Antes — Agente IA de Consultoría</span>
                </div>
            </div>

        </div>
    </div>

</body>
</html>