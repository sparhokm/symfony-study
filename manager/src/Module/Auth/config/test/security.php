<?php

use App\Module\Auth\Domain\Entity\User\User;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    // auto hasher with default options for the User class (and children)
    $security->passwordHasher(User::class)
        ->algorithm('auto')
        ->cost(4) // Lowest possible value for bcrypt
        ->timeCost(3) // Lowest possible value for argon
        ->memoryCost(10) // Lowest possible value for argon
    ;
};
