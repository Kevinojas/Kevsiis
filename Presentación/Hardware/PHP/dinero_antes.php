<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinero Antes — Agente IA de Consultoría</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .gradient-text { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .accent-blue { background-color: #3b82f6; }
        .card { transition: all 0.3s ease; border: 1px solid rgba(59, 130, 246, 0.1); }
        .card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15); }
        .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4); }
        .input-field { transition: all 0.3s ease; border: 2px solid #e2e8f0; }
        .input-field:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .pulse-dot { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        .progress-bar { transition: width 1.5s ease-in-out; }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .typing-dot { width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; animation: typing 1.4s infinite ease-in-out; }
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-8px); } }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.1; }
        .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%); }
        .nav-link { transition: all 0.2s ease; }
        .nav-link:hover { color: #60a5fa; }
        .result-card { border-left: 4px solid #3b82f6; }
        .chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .chat-msg.bot { background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 4px; }
        .chat-msg.user { background: #3b82f6; color: white; border-bottom-right-radius: 4px; align-self: flex-end; }
        .agent-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>
</head>
<body>
<?php
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

    if ($margen_porcentual >= 40) { $score += 25;
    } elseif ($margen_porcentual >= 20) { $score += 15;
    } elseif ($margen_porcentual > 0) { $score += 5;
    } else { $razones[] = "Margen negativo: vendés a pérdida por unidad."; }

    if ($ganancia_neta_mensual > 0) {
        if ($ganancia_neta_mensual > $cf) { $score += 30;
        } elseif ($ganancia_neta_mensual > $cf * 0.5) { $score += 20;
        } else { $score += 10; }
    } else { $razones[] = "Ganancia neta mensual negativa o nula."; }

    if ($meses_recupero <= 6) { $score += 20;
    } elseif ($meses_recupero <= 12) { $score += 15;
    } elseif ($meses_recupero <= 24) { $score += 5;
    } else { $razones[] = "La inversión no se recupera en menos de 2 años."; }

    if ($comp <= 3) { $score += 10;
    } elseif ($comp <= 10) { $score += 5;
    } else { $razones[] = "Mercado muy competitivo."; }

    if ($participacion_necesaria <= 1) { $score += 15;
    } elseif ($participacion_necesaria <= 5) { $score += 10;
    } elseif ($participacion_necesaria <= 15) { $score += 5;
    } else { $razones[] = "Necesitás una porción de mercado muy alta."; }

    if ($score >= 80) { $decision = "SÍ GENERA DINERO"; $confianza = "ALTA";
    } elseif ($score >= 60) { $decision = "POSIBLE — pero con riesgos"; $confianza = "MEDIA";
    } else { $decision = "NO GENERA DINERO"; $confianza = "BAJA"; }

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

<!-- Navbar -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center h-16">
            <a href="../../.." class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg accent-blue flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900">Flowith<span class="text-blue-600">Agent</span></span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="../../.." class="nav-link text-sm text-gray-500 hover:text-blue-600">Inicio</a>
                <span class="text-sm font-semibold text-blue-600">Dinero Antes</span>
            </div>
        </div>
    </div>
</nav>

<div class="min-h-screen py-8 px-4">
    <div class="max-w-5xl mx-auto">

        <?php if ($resultado): ?>

        <!-- RESULTADOS -->
        <div class="fade-in space-y-6 mb-8">

            <!-- Score Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-medium mb-4">
                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></div>
                    Análisis completado
                </div>
                <h1 class="text-4xl font-bold text-gray-900">Resultado del <span class="gradient-text">Análisis</span></h1>
                <p class="text-gray-500 mt-2">Agente IA evaluó <?= htmlspecialchars($datos['nombre']) ?> en 5 dimensiones</p>
            </div>

            <!-- Score Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden fade-in">
                <div class="p-8 text-center" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                    <div class="inline-flex items-center justify-center w-28 h-28 rounded-full accent-blue shadow-lg mb-4">
                        <span class="text-4xl font-bold text-white"><?= $resultado['score'] ?></span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Score: <?= $resultado['score'] ?>/100</h2>
                    <div class="w-full max-w-md mx-auto bg-gray-200 rounded-full h-4 mb-4">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-4 rounded-full progress-bar" style="width: <?= $resultado['score'] ?>%"></div>
                    </div>
                    <div class="text-2xl font-bold mb-3">
                        <?php if ($resultado['score'] >= 80): ?>
                            <span class="text-green-600">✅ <?= $resultado['decision'] ?></span>
                        <?php elseif ($resultado['score'] >= 60): ?>
                            <span class="text-yellow-600">⚠️ <?= $resultado['decision'] ?></span>
                        <?php else: ?>
                            <span class="text-red-600">❌ <?= $resultado['decision'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="inline-flex items-center bg-white rounded-full px-5 py-2 shadow-sm">
                        <div class="w-3 h-3 rounded-full bg-blue-600 pulse-dot mr-2"></div>
                        <span class="text-gray-700 font-medium">Confianza: <?= $resultado['confianza'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Métricas clave -->
            <div class="bg-white rounded-2xl p-8 shadow-lg fade-in">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg accent-blue flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Métricas clave</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Precio venta</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['precio'], 0) ?></div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Costo unitario</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['costo'], 0) ?></div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Margen unitario</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($resultado['margen_unitario'], 0) ?></div>
                        <div class="text-sm text-blue-600 font-medium"><?= $resultado['margen_porcentual'] ?>%</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Ventas mensuales</div>
                        <div class="text-2xl font-bold text-gray-800"><?= number_format($datos['ventas_mensuales']) ?></div>
                        <div class="text-sm text-gray-500">unidades</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Contribución mensual</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($resultado['contribucion_mensual'], 0) ?></div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Costos fijos/mes</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['costos_fijos'], 0) ?></div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Ganancia neta/mes</div>
                        <div class="text-2xl font-bold <?= $resultado['ganancia_neta_mensual'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            $<?= number_format($resultado['ganancia_neta_mensual'], 0) ?>
                        </div>
                        <div class="text-sm text-blue-600 font-medium"><?= $resultado['margen_neto_porcentual'] ?>%</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-5 text-center">
                        <div class="text-sm text-gray-500 mb-1">Inversión inicial</div>
                        <div class="text-2xl font-bold text-gray-800">$<?= number_format($datos['inversion'], 0) ?></div>
                    </div>
                </div>
            </div>

            <!-- Indicadores -->
            <div class="bg-white rounded-2xl p-8 shadow-lg fade-in">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg accent-blue flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Indicadores</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 mb-1">Punto de equilibrio</div>
                        <div class="text-xl font-bold text-gray-800">
                            <?= is_infinite($resultado['punto_eq_unidades']) ? 'Nunca (margen negativo)' : number_format($resultado['punto_eq_unidades']) . ' unid./mes' ?>
                        </div>
                    </div>
                    <div class="p-5 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 mb-1">Recupero de inversión</div>
                        <div class="text-xl font-bold text-gray-800">
                            <?= is_infinite($resultado['meses_recupero']) ? 'No se recupera' : '~' . $resultado['meses_recupero'] . ' meses' ?>
                        </div>
                        <?php if ($resultado['recupera_en_12m']): ?>
                            <div class="text-xs text-green-600 font-medium mt-1">✅ Recuperable en 12 meses</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 mb-1">Participación de mercado</div>
                        <div class="text-xl font-bold text-gray-800"><?= $resultado['participacion_necesaria'] ?>%</div>
                        <div class="text-xs text-gray-500 mt-1">de <?= number_format($datos['tamano_mercado']) ?> clientes</div>
                    </div>
                    <div class="p-5 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 mb-1">Competidores</div>
                        <div class="text-xl font-bold text-gray-800"><?= $datos['competidores'] ?></div>
                        <div class="text-xs text-gray-500 mt-1">directos</div>
                    </div>
                </div>
            </div>

            <!-- Advertencias -->
            <?php if (!empty($resultado['razones'])): ?>
            <div class="bg-white rounded-2xl p-8 shadow-lg fade-in">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg bg-yellow-400 flex items-center justify-center mr-3">
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
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg fade-in">
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
                <a href="" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Analizar otro producto
                </a>
            </div>
        </div>

        <?php else: ?>

        <!-- FORMULARIO -->
        <div class="text-center mb-10 fade-in">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-medium mb-4">
                <div class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></div>
                Agente IA Online
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                <span class="gradient-text">Dinero Antes</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Agente IA que analiza si tu producto genera dinero antes de gastar un peso argentino.</p>
        </div>

        <!-- Chat-like form -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden fade-in">
            <div class="p-6" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                <div class="flex items-center">
                    <div class="agent-avatar mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Agente Dinero Antes</h2>
                        <p class="text-gray-600 text-sm">Completá los datos y el agente IA evaluará la viabilidad de tu producto.</p>
                    </div>
                </div>
            </div>

            <form method="POST" class="p-8 space-y-5">
                <input type="hidden" name="analizar" value="1">

                <div class="flex items-start space-x-3 bg-blue-50 rounded-xl p-4 border-l-4 border-blue-500">
                    <div class="agent-avatar" style="width:28px;height:28px;font-size:10px;font-weight:700;color:white;">AI</div>
                    <div>
                        <p class="text-sm text-gray-700">¿Cómo se llama tu producto?</p>
                        <input type="text" name="nombre" value="Mi producto"
                               class="input-field mt-2 w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800 text-sm font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">$</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">💰 Costo de producir/vender una unidad (ARS)</label>
                            <input type="number" name="costo" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">$</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">🏷️ Precio de venta por unidad (ARS)</label>
                            <input type="number" name="precio" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">#</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">📦 Ventas estimadas por mes (unidades)</label>
                            <input type="number" name="ventas_mensuales" min="0" required
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">$</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">🏢 Costos fijos mensuales (ARS)</label>
                            <input type="number" name="costos_fijos" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">$</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">💵 Inversión inicial necesaria (ARS)</label>
                            <input type="number" name="inversion" step="0.01" min="0" required
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600"><</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">👥 ¿Cuántos competidores directos tenés?</label>
                            <input type="number" name="competidores" min="0" value="0"
                                   class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                        </div>
                    </div>
                </div>

                <div class="flex items-start space-x-3 bg-gray-50 rounded-xl p-4">
                    <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">🌍</div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">🌍 Tamaño estimado del mercado (clientes potenciales)</label>
                        <input type="number" name="tamano_mercado" min="1" value="1000"
                               class="input-field w-full px-4 py-2.5 rounded-lg bg-white outline-none text-gray-800">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="btn-primary w-full py-4 text-white font-bold text-lg rounded-xl inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        Analizar con IA
                    </button>
                </div>
            </form>
        </div>

        <!-- Info cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 fade-in">
            <div class="bg-white rounded-2xl p-6 shadow-sm card">
                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl accent-blue flex items-center justify-center mr-4 flex-shrink-0">
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
            <div class="bg-white rounded-2xl p-6 shadow-sm card">
                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl accent-blue flex items-center justify-center mr-4 flex-shrink-0">
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
            <div class="bg-white rounded-2xl p-6 shadow-sm card">
                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl accent-blue flex items-center justify-center mr-4 flex-shrink-0">
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
                <div class="w-8 h-8 rounded-lg accent-blue flex items-center justify-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Dinero Antes — Agente IA de Consultoría by Flowith Agent AI</span>
            </div>
        </div>

    </div>
</div>

</body>
</html>
