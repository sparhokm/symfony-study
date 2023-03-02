<?php

declare(strict_types=1);

namespace App\Http\Infrastructure\Security\OAuth\Vk;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/** @psalm-suppress PropertyNotSetInConstructor */
final class Provider extends AbstractProvider
{
    private const BASE_OAUTH_URI = 'https://oauth.vk.com';
    private const BASE_URI = 'https://api.vk.com/method';
    private const VERSION = 5.131;

    /**
     * @var list<string>
     * @see https://dev.vk.com/reference/access-rights
     */
    private array $scopes = [
        'email',
    ];

    /**
     * @var list<string>
     * @see https://dev.vk.com/reference/objects/user
     */
    private array $userFields = [
        'id',
    ];

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl(): string
    {
        return self::BASE_OAUTH_URI . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::BASE_OAUTH_URI . '/access_token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        $params = [
            'fields' => $this->userFields,
            'access_token' => $token->getToken(),
            'v' => self::VERSION,
        ];
        $query = $this->buildQueryString($params);

        return self::BASE_URI . '/users.get?' . $query;
    }

    protected function getDefaultScopes(): array
    {
        return $this->scopes;
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!\is_array($data)) {
            throw new IdentityProviderException('vk error', $response->getStatusCode(), $data);
        }

        $errorCode = !empty($data['error_code']) ? (int)$data['error_code'] : null;
        $errorMessage = !empty($data['error_msg']) ? (string)$data['error_msg'] : 'vk error';

        if ($errorCode) {
            throw new IdentityProviderException($errorMessage, $errorCode, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): User
    {
        /** @var array{user_id:int, email?: ?string} $vkData */
        $vkData = $token->getValues();

        return new User($vkData);
    }
}
