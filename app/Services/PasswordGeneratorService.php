<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * Genera una contraseña temporal legible pero segura.
     * Formato: Palabra + Símbolo + Número + Letras (ej: Salud#2024Abc)
     */
    public static function generateTemporaryPassword(): string
    {
        $words = [
            'Salud', 'Vida', 'Núcleo', 'Vital', 'Fuerza',
            'Energía', 'Flujo', 'Ritmo', 'Pulso', 'Tono',
            'Forma', 'Célula', 'Fibra', 'Músculo', 'Hueso',
        ];

        $word = $words[array_rand($words)];
        $number = random_int(2000, 2050);
        $symbols = ['#', '$', '@', '!', '%'];
        $symbol = $symbols[array_rand($symbols)];
        $suffix = strtoupper(chr(random_int(65, 90))) . strtolower(chr(random_int(97, 122))) . strtolower(chr(random_int(97, 122)));

        return "{$word}{$symbol}{$number}{$suffix}";
    }
}
