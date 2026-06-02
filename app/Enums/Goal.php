<?php

namespace App\Enums;

enum Goal: string
{
    case Maintain = 'maintain';
    case Lose = 'lose';
    case Gain = 'gain';

    public function label(): string
    {
        return match ($this) {
            self::Maintain => 'Manter peso',
            self::Lose => 'Perder gordura',
            self::Gain => 'Ganhar massa',
        };
    }
}
