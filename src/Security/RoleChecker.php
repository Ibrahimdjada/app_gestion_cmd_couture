<?php

namespace App\Security;

use App\Entity\User;

class RoleChecker
{
    /**
     * Simple helper: returns true if the user has any of the provided roles.
     * Usage: \App\Security\RoleChecker::hasAny($this->getUser(), [Constantes::ROLE_ADMIN, ...])
     *
     * @param User|null $user
     * @param array $roles
     * @return bool
     */
    public static function hasAny(?User $user, array $roles): bool
    {
        if (!$user) {
            return false;
        }

        $userRoles = $user->getRoles();
        foreach ($roles as $r) {
            if (in_array($r, $userRoles, true)) {
                return true;
            }
        }

        return false;
    }
}
