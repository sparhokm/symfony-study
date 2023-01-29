<?php

use App\Auth\Domain\Entity\User\User;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    // auto hasher with default options for the User class (and children)
    $security->passwordHasher(User::class)
        ->algorithm('auto')
    ;
};
