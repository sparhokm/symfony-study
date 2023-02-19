<?php

namespace App\Http\Infrastructure\Security;

use App\Http\Application\Exception\AccessDenied;
use App\Http\Application\Exception\LoginException;
use App\Http\Domain\Entity\UserIdentity;
use App\Module\Auth\Application\Command\PasswordValidate;
use App\Module\Auth\Domain\Exception\User\PasswordIncorrect;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserProviderInterface $userProvider,
        private readonly PasswordValidate\Handler $passwordHashHandler,
    ) {
    }

    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') !== 'auth_login') {
            return false;
        }

        return $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $userBadge = new UserBadge($credentials['username'], $this->userProvider->loadUserByIdentifier(...));

        return new Passport(
            $userBadge,
            new CustomCredentials($this->checkCredentials(...), $credentials['password']),
            [new RememberMeBadge()]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        throw new LoginException(previous: $exception);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        throw new AccessDenied();
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('auth_login');
    }

    protected function checkCredentials(string $password, UserIdentity $userIdentity): bool
    {
        try {
            $this->passwordHashHandler->handle(
                new PasswordValidate\Command(
                    password: $password,
                    passwordHash: $userIdentity->getPassword() ?? ''
                )
            );

            return true;
        } catch (PasswordIncorrect) {
            throw new LoginException();
        }
    }

    /**
     * @return array{username: string, password: string}
     */
    private function getCredentials(Request $request): array
    {
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        if (!$data instanceof stdClass) {
            throw new BadRequestHttpException('Invalid JSON.');
        }

        $credentials = [];

        try {
            if (!\is_string($data->email)) {
                throw new BadRequestHttpException(sprintf('The key "%s" must be a string.', 'email'));
            }
            $credentials['username'] = $data->email;
        } catch (AccessException $e) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be provided.', 'email'), $e);
        }

        try {
            if (!\is_string($data->password)) {
                throw new BadRequestHttpException(sprintf('The key "%s" must be a string.', 'password'));
            }
            $credentials['password'] = $data->password;
        } catch (AccessException $e) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be provided.', 'password'), $e);
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }
}
