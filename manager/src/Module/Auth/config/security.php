<?php

use App\Module\Auth\Domain\Entity\User\User;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->passwordHasher(User::class)->algorithm('auto');
};
