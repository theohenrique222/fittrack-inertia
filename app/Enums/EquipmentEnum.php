<?php

namespace App\Enums;

enum EquipmentEnum: string
{
    case Dumbbell = 'Dumbbell';
    case Barbell = 'Barbell';
    case Machine = 'Machine';
    case Cable = 'Cable';
    case Bodyweight = 'Bodyweight';
    case ResistanceBand = 'Resistance Band';
    case Kettlebell = 'Kettlebell';
    case SmithMachine = 'Smith Machine';

    public function label(): string
    {
        return $this->value;
    }
}
