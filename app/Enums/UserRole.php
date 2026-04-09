<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PERSONAL = 'personal';
    case TRAINER = 'trainer';
    case CLIENT = 'client';
    case SELF = 'self';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PERSONAL => 'Personal Trainer',
            self::TRAINER => 'Treinador',
            self::CLIENT => 'Cliente',
            self::SELF => 'Freelancer',
        };
    }
}

