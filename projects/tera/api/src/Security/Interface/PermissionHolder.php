<?php

namespace App\Security\Interface;

interface PermissionHolder
{
    /**
     * @return array<string>
     */
    public function getPermissions(): array;
}
