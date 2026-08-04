<?php

namespace App\Security;

class Roles
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_EDITOR = 'ROLE_EDITOR';

    public const ASSIGNABLE = [
        self::ROLE_ADMIN,
        self::ROLE_EDITOR,
    ];
}
