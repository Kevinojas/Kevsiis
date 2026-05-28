<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowith Agent AI — Consultoría Inteligente</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%); }
        .gradient-text { background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-text-gold { background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .accent-blue { background-color: #3b82f6; }
        .accent-gold { background-color: #f59e0b; }
        .card { transition: all 0.3s ease; border: 1px solid rgba(59, 130, 246, 0.1); }
        .card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3); }
        .glow { box-shadow: 0 0 40px rgba(59, 130, 246, 0.3); }
        .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4); }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; }
        .chat-widget { position: fixed; bottom: 24px; right: 24px; z-index: 50; }
        .chat-bubble { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4); transition: all 0.3s ease; }
        .chat-bubble:hover { transform: scale(1.1); }
        .chat-panel { position: fixed; bottom: 100px; right: 24px; width: 380px; height: 520px; background: white; border-radius: 16px; box-shadow: 0 10px 60px rgba(0,0,0,0.2); display: none; flex-direction: column; overflow: hidden; z-index: 51; }
        .chat-panel.open { display: flex; }
        .chat-header { background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); padding: 16px 20px; }
        .chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .chat-msg.bot { background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 4px; }
        .chat-msg.user { background: #3b82f6; color: white; border-bottom-right-radius: 4px; align-self: flex-end; }
        .typing-dot { width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; animation: typing 1.4s infinite ease-in-out; }
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-8px); } }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }
        .fade-in-delay-4 { animation-delay: 0.4s; }
        .stat-number { font-size: 2.5rem; font-weight: 800; }
        @media (max-width: 640px) { .chat-panel { width: calc(100vw - 32px); right: 16px; bottom: 90px; height: 460px; } }
        .agent-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="fixed top-0 w-full z-40 bg-white/90 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-2">
                <div class="w-9 h-9 rounded-lg accent-blue flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">Flowith<span class="text-blue-600">Agent</span></span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#inicio" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Inicio</a>
                <a href="#agentes" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Agentes IA</a>
                <a href="#como-funciona" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Cómo Funciona</a>
                <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition font-semibold">Dinero Antes →</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero -->
<section id="inicio" class="gradient-bg min-h-screen flex items-center relative overflow-hidden pt-16">
    <div class="blob w-96 h-96 bg-blue-500 top-[-10%] left-[-5%]"></div>
    <div class="blob w-80 h-80 bg-indigo-500 bottom-[-10%] right-[-5%]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="fade-in">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-900/50 border border-blue-500/30 text-blue-300 text-sm font-medium mb-6">
                    <div class="w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></div>
                    IA Consultiva 24/7
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    Tu <span class="gradient-text">Agente IA</span> que analiza si tu producto genera dinero
                </h1>
                <p class="text-lg text-gray-300 mb-8 max-w-xl">
                    Antes de gastar un peso, nuestro agente de inteligencia artificial evalúa la viabilidad de tu producto en segundos. Basado en datos, no en corazonadas.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="btn-primary px-8 py-4 rounded-xl text-white font-semibold text-lg inline-flex items-center">
                        Probar Agente IA
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </a>
                    <a href="Presentacion/" class="px-8 py-4 rounded-xl border border-gray-600 text-gray-300 font-semibold text-lg hover:border-blue-500 hover:text-blue-400 transition inline-flex items-center">
                        Conocer más
                    </a>
                </div>
            </div>
            <div class="fade-in fade-in-delay-2 hidden lg:block">
                <div class="relative">
                    <div class="glow absolute inset-0 rounded-2xl"></div>
                    <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-8 border border-white/10 relative">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="agent-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-semibold">Agente Dinero Antes</div>
                                <div class="text-gray-400 text-sm">Online · IA Consultiva</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="agent-avatar mt-1" style="width:28px;height:28px;font-size:11px;font-weight:700;color:white;">AI</div>
                                <div class="chat-msg bot" style="background:rgba(255,255,255,0.1);color:#e2e8f0;">¡Hola! Soy tu agente de consultoría IA. Analicemos tu producto. ¿Cuál es tu costo por unidad?</div>
                            </div>
                            <div class="flex items-start space-x-3 justify-end">
                                <div class="chat-msg user" style="background:#3b82f6;">Mi costo es de $500 ARS por unidad</div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="agent-avatar mt-1" style="width:28px;height:28px;font-size:11px;font-weight:700;color:white;">AI</div>
                                <div class="chat-msg bot" style="background:rgba(255,255,255,0.1);color:#e2e8f0;">Excelente. ¿Y cuál es el precio de venta al público?</div>
                            </div>
                            <div class="flex justify-center mt-4">
                                <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-600/30 text-blue-300 text-sm">
                                    <div class="typing-dot mr-1"></div>
                                    <div class="typing-dot mr-1"></div>
                                    <div class="typing-dot"></div>
                                    <span class="ml-2">Analizando viabilidad...</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-white/10 text-center">
                            <div class="text-3xl font-bold gradient-text">85/100</div>
                            <div class="text-gray-400 text-sm mt-1">Score de viabilidad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-16 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="fade-in">
                <div class="stat-number gradient-text">5</div>
                <div class="text-gray-600 font-medium mt-1">Dimensiones de análisis</div>
            </div>
            <div class="fade-in fade-in-delay-1">
                <div class="stat-number gradient-text">100</div>
                <div class="text-gray-600 font-medium mt-1">Puntos de score</div>
            </div>
            <div class="fade-in fade-in-delay-2">
                <div class="stat-number gradient-text">24/7</div>
                <div class="text-gray-600 font-medium mt-1">Disponible siempre</div>
            </div>
            <div class="fade-in fade-in-delay-3">
                <div class="stat-number gradient-text">0</div>
                <div class="text-gray-600 font-medium mt-1">Sin gastar un peso</div>
            </div>
        </div>
    </div>
</section>

<!-- Agentes IA -->
<section id="agentes" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Nuestros <span class="gradient-text">Agentes IA</span></h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Agentes de inteligencia artificial especializados en consultoría de negocios</p>
        </div>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="card bg-white rounded-2xl p-8 shadow-sm fade-in">
                <div class="w-14 h-14 rounded-xl accent-blue flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Dinero Antes</h3>
                <p class="text-gray-600 mb-6">Agente IA que analiza si tu producto genera dinero antes de invertir. Evalúa margen, ganancia, recupero, competencia y mercado en segundos.</p>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Score de viabilidad 0-100</div>
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>5 dimensiones de análisis</div>
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Recomendaciones inteligentes</div>
                </div>
                <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="btn-primary w-full py-3 rounded-xl text-white font-semibold text-center inline-block">Usar Agente →</a>
            </div>
            <div class="card bg-white rounded-2xl p-8 shadow-sm fade-in fade-in-delay-1">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Flowith Oracle</h3>
                <p class="text-gray-600 mb-6">Agente autónomo de última generación con contexto ilimitado y capacidad de ejecución multi-paso. Ideal para proyectos complejos de consultoría.</p>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Contexto ilimitado</div>
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Agente autónomo</div>
                    <div class="flex items-center text-sm text-gray-600"><svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Knowledge Garden integrado</div>
                </div>
                <a href="Presentacion/" class="w-full py-3 rounded-xl border-2 border-blue-600 text-blue-600 font-semibold text-center inline-block hover:bg-blue-50 transition">Ver presentación →</a>
            </div>
        </div>
    </div>
</section>

<!-- Cómo Funciona -->
<section id="como-funciona" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Cómo <span class="gradient-text">Funciona</span>?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">En 3 pasos simples, nuestro agente IA evalúa tu producto</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center fade-in">
                <div class="w-16 h-16 rounded-full accent-blue flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-2xl font-bold text-white">1</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Ingresá tus datos</h3>
                <p class="text-gray-600">Completá el formulario con costo, precio, ventas estimadas y costos fijos de tu producto.</p>
            </div>
            <div class="text-center fade-in fade-in-delay-1">
                <div class="w-16 h-16 rounded-full accent-blue flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-2xl font-bold text-white">2</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Análisis con IA</h3>
                <p class="text-gray-600">Nuestro motor evalúa 5 dimensiones clave: margen, ganancia, recupero, competencia y mercado.</p>
            </div>
            <div class="text-center fade-in fade-in-delay-2">
                <div class="w-16 h-16 rounded-full accent-blue flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-2xl font-bold text-white">3</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Recibí el resultado</h3>
                <p class="text-gray-600">Obtené un score de viabilidad, decisiones claras y advertencias inteligentes en segundos.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 gradient-bg relative overflow-hidden">
    <div class="blob w-80 h-80 bg-blue-500 top-[-20%] right-[-10%]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl font-bold text-white mb-6 fade-in">¿Listo para saber si tu producto <span class="gradient-text">genera dinero</span>?</h2>
        <p class="text-lg text-gray-300 mb-8 max-w-2xl mx-auto fade-in fade-in-delay-1">Antes de gastar un peso argentino, dejá que la IA analice la viabilidad de tu producto.</p>
        <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="btn-primary px-10 py-4 rounded-xl text-white font-semibold text-lg inline-flex items-center fade-in fade-in-delay-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
            Probar el Agente IA ahora
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 py-12 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-2 mb-4 md:mb-0">
                <div class="w-9 h-9 rounded-lg accent-blue flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">Flowith<span class="text-blue-500">Agent</span></span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-gray-400">
                <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="hover:text-white transition">Dinero Antes</a>
                <a href="Presentacion/" class="hover:text-white transition">Presentación</a>
            </div>
            <div class="text-sm text-gray-500 mt-4 md:mt-0">Flowith Agent AI — Consultoría Inteligente</div>
        </div>
    </div>
</footer>

<!-- Chat Widget -->
<div class="chat-widget" id="chatWidget">
    <div class="chat-panel" id="chatPanel">
        <div class="chat-header">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div class="text-white font-semibold">Asistente IA</div>
                    <div class="text-blue-200 text-xs">Online · Consultoría</div>
                </div>
                <button onclick="toggleChat()" class="ml-auto text-white/60 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex-1 p-4 overflow-y-auto space-y-4" id="chatMessages">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="chat-msg bot">¡Hola! 👋 Soy el asistente de Flowith Agent AI. ¿Querés que analice tu producto con nuestro agente Dinero Antes? Hacé clic en "Analizar" para empezar.</div>
            </div>
        </div>
        <div class="p-4 border-t border-gray-100">
            <a href="Presentacion/Hardware/PHP/dinero_antes.php" class="btn-primary w-full py-3 rounded-xl text-white font-semibold text-center inline-flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
                Analizar con IA
            </a>
        </div>
    </div>
    <div class="chat-bubble" onclick="toggleChat()">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
    </div>
</div>

<script>
function toggleChat() {
    document.getElementById('chatPanel').classList.toggle('open');
}
</script>
</body>
</html>
