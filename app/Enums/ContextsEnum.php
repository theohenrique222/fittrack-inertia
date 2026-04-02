<?php

namespace App\Enums;

enum ContextsEnum: string
{
    case ADMIN = 'admin';
    case PERSONAL = 'personal';

    public function getMainPageUrl(): string
    {
        return match ($this) {
            self::ADMIN => 'Painel Administrativo',
            self::PERSONAL => 'Painel do Personal'
        };
    }
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Painel Administrativo',
            self::PERSONAL => 'Painel do Personal',
        };
    }
}
