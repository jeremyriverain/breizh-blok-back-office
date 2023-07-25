<?php

namespace App\Controller\Utils;

enum Roles: string
{
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
