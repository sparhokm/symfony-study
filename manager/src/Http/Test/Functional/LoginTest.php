<?php

declare(strict_types=1);

namespace App\Http\Test\Functional;

use App\Http\Application\Exception\LoginException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
final class LoginTest extends WebTestCase
{
    private const LOGIN_URI = '/auth/login';
    private const PROTECTED_URI = '/auth/user';
    private const EMAIL = 'user@app.test';
    private const PASSWORD = 'password';

    public function testGet(): void
    {
        $this->createClient()->jsonRequest('GET', self::LOGIN_URI);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $client = $this->createClient();
        $client->jsonRequest('POST', self::LOGIN_URI, ['email' => self::EMAIL, 'password' => self::PASSWORD]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasCookie('REMEMBERME');

        $data = (array)json_decode($client->getResponse()->getContent() ?: '{}', true);

        self::assertEquals([], $data);
    }

    public function testSuccessRemember(): void
    {
        $client = $this->createClient();

        $client->jsonRequest('POST', self::LOGIN_URI, ['email' => self::EMAIL, 'password' => self::PASSWORD]);
        $rememberMeCookie = $client->getCookieJar()->get('REMEMBERME');
        self::assertNotEmpty($rememberMeCookie);
        $client->restart();

        $client->jsonRequest('GET', self::PROTECTED_URI);
        $this->assertResponseStatusCodeSame(401);

        $client->getCookieJar()->set($rememberMeCookie);
        $client->jsonRequest('GET', self::PROTECTED_URI, []);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testNoneFullInput(): void
    {
        $client = $this->createClient();
        $client->jsonRequest('POST', self::LOGIN_URI, ['password' => self::PASSWORD]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testNotValidUser(): void
    {
        $client = $this->createClient();
        $client->jsonRequest('POST', self::LOGIN_URI, ['email' => 'wrong@app.test', 'password' => self::PASSWORD]);

        $this->assertResponseStatusCodeSame(400);

        $data = (array)json_decode($client->getResponse()->getContent() ?: '{}', true);

        self::assertEquals(['errors' => [LoginException::MESSAGE]], $data);
    }

    public function testNotValidPassword(): void
    {
        $client = $this->createClient();
        $client->jsonRequest('POST', self::LOGIN_URI, ['email' => self::EMAIL, 'password' => 'wrongPassword']);

        $this->assertResponseStatusCodeSame(400);

        $data = (array)json_decode($client->getResponse()->getContent() ?: '{}', true);

        self::assertEquals(['errors' => [LoginException::MESSAGE]], $data);
    }
}
