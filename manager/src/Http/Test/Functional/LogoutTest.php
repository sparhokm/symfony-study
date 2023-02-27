<?php

declare(strict_types=1);

namespace App\Http\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
final class LogoutTest extends WebTestCase
{
    private const LOGOUT_URI = '/auth/logout';
    private const LOGIN_URI = '/auth/login';
    private const PROTECTED_URI = '/auth/user';
    private const EMAIL = 'user@app.test';
    private const PASSWORD = 'password';

    public function testSuccess(): void
    {
        $client = $this->createClient();

        $client->jsonRequest('POST', self::LOGIN_URI, ['email' => self::EMAIL, 'password' => self::PASSWORD]);
        $client->jsonRequest('GET', self::PROTECTED_URI);
        $this->assertResponseStatusCodeSame(200);

        $client->jsonRequest('GET', self::LOGOUT_URI);
        $this->assertBrowserNotHasCookie('REMEMBERME');
        $this->assertResponseStatusCodeSame(200);

        $client->jsonRequest('GET', self::PROTECTED_URI);
        $this->assertResponseStatusCodeSame(401);
    }
}
