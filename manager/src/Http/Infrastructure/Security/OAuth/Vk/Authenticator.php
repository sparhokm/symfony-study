<?php

declare(strict_types=1);

namespace App\Http\Infrastructure\Security\OAuth\Vk;

use App\Http\Application\Exception\AccessDenied;
use App\Http\Application\Exception\LoginException;
use App\Http\Infrastructure\Security\UserProvider;
use App\Module\Auth\Application\Command\JoinByNetwork;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class Authenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly ClientRegistry $clientRegistry,
        private readonly JoinByNetwork\Handler $joinByNetworkHandler,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oauth_vk_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('vk');

        /** @var AccessToken $accessToken */
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var User $vkUser */
                $vkUser = $client->fetchUserFromToken($accessToken);

                $email = (string)$vkUser->getEmail();

                try {
                    return $this->userProvider->loadUserByIdentifier($email);
                } catch (UserNotFoundException) {
                    $this->joinByNetworkHandler->handle(
                        new JoinByNetwork\Command($email, 'vk', $vkUser->getId())
                    );

                    return $this->userProvider->loadUserByIdentifier($email);
                }
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse(['errors' => [LoginException::MESSAGE]], 400);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        throw new AccessDenied();
    }
}
