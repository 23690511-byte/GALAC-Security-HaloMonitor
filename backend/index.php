<?php
/**
 * HaloMonitor - Backend API Root
 * GALAC Security
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/helpers/response.php';

jsonResponse(200, [
    'ok' => true,
    'message' => 'HaloMonitor API Gateway en línea - GALAC Security',
    'data' => [
        'sistema' => 'HaloMonitor Web Platform',
        'version' => '3.0.0',
        'documento_origen' => 'Proyecto Final Integrado (Unidades 2, 3 y 4)',
        'autores' => [
            'Gerardo Axel Castillo Balderas',
            'Luis Roberto Martínez Méndez',
            'Luis Antonio Ponce Navarro',
            'Carlos Reyes Manuel'
        ],
        'docente' => 'Dr. Nitgard Zapata Garay',
        'zona_horaria' => date_default_timezone_get(),
        'hora_servidor' => date('Y-m-d H:i:s')
    ]
]);
