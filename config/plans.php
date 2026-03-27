<?php

return [
    'currency' => 'USD',
    'default_plan' => 'free',
    'catalog' => [
        'free' => [
            'key' => 'free',
            'name' => 'Metabole Free',
            'price' => 0,
            'period' => 'siempre',
            'badge' => null,
            'description' => 'Ideal para nutricionistas que inician su camino digital.',
            'cta' => 'Comenzar Gratis',
            'cta_type' => 'muted',
            'features' => [
                'Hasta 5 pacientes activos',
                'Historial clinico basico',
                'Acceso a base de alimentos',
            ],
        ],
        'normal' => [
            'key' => 'normal',
            'name' => 'Metabole Normal',
            'price' => 29,
            'period' => 'mes',
            'badge' => 'Recomendado',
            'description' => 'La solucion completa para profesionales en crecimiento.',
            'cta' => 'Suscribirse Ahora',
            'cta_type' => 'primary',
            'features' => [
                'Pacientes ilimitados',
                'Editor dinamico de planes',
                'Seguimiento de metricas avanzado',
                'App para pacientes incluida',
            ],
        ],
        'pro' => [
            'key' => 'pro',
            'name' => 'Metabole Pro',
            'price' => 59,
            'period' => 'mes',
            'badge' => null,
            'description' => 'Para clinicas que buscan el maximo nivel de excelencia.',
            'cta' => 'Contactar Ventas',
            'cta_type' => 'outline',
            'features' => [
                'Personalizacion total de marca',
                'WhatsApp automaticos ilimitados',
                'Soporte Premium 24/7',
                'API para integracion propia',
            ],
        ],
    ],
];
