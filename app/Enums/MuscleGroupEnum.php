<?php

namespace App\Enums;

enum MuscleGroupEnum: string
{
    case Chest = 'Chest';
    case Back = 'Back';
    case Shoulders = 'Shoulders';
    case Biceps = 'Biceps';
    case Triceps = 'Triceps';
    case Forearms = 'Forearms';
    case Abs = 'Abs';
    case Quadriceps = 'Quadriceps';
    case Hamstrings = 'Hamstrings';
    case Glutes = 'Glutes';
    case Calves = 'Calves';
    case FullBody = 'Full Body';

    public function label(): string
    {
        return $this->value;
    }
}
