<?php

namespace App\Utils;

enum Roles: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CONTRIBUTOR = 'ROLE_CONTRIBUTOR';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    case USER = 'ROLE_USER';
}
