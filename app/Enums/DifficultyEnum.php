<?php

namespace App\Enums;

enum DifficultyEnum: string
{
    case Beginner = 'Beginner';
    case Intermediate = 'Intermediate';
    case Advanced = 'Advanced';

    public function label(): string
    {
        return $this->value;
    }
}
