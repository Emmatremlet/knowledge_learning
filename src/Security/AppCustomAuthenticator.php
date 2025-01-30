<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Psr\Log\LoggerInterface;


/**
 * AppCustomAuthenticator
 * Gestionnaire personnalisé pour l'authentification des utilisateurs.
 */
class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * @var string La route de connexion utilisée pour l'authentification.
     */
    public const LOGIN_ROUTE = 'app_login';

    private LoggerInterface $logger;

    /**
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL pour les redirections.
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Gère l'authentification d'une requête.
     *
     * @param Request $request Requête HTTP contenant les informations d'authentification.
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $csrfToken = $request->request->get('_csrf_token');

        $this->logger->info('CSRF Token at the start of authentication: ' . $csrfToken);

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $passport = new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password')),
            [
                // new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );

        $this->logger->error('CSRF Token at the end of authentication: ' . $csrfToken);

        return $passport;
    }

    /**
     * Gère la redirection après une authentification réussie.
     *
     * @param Request $request Requête HTTP.
     * @param TokenInterface $token Jeton d'authentification.
     * @param string $firewallName Nom du pare-feu utilisé.
     * @return Response|null Réponse de redirection ou null.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * Retourne l'URL de connexion.
     *
     * @param Request $request Requête HTTP.
     * @return string L'URL de connexion.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}