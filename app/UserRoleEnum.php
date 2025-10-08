<?php

namespace App;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
}
