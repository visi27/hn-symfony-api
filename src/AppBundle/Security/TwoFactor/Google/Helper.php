<?php
namespace AppBundle\Security\TwoFactor\Google;

use AppBundle\Security\TwoFactor\HelperInterface;
use Google\Authenticator\GoogleAuthenticator as BaseGoogleAuthenticator;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class Helper implements HelperInterface
{
    /**
     * @var string $server
     */
    protected $server;

    /**
     * @var \Google\Authenticator\GoogleAuthenticator $authenticator
     */
    protected $authenticator;

    /**
     * Construct the helper service for Google Authenticator
     * @param string $server
     * @param \Google\Authenticator\GoogleAuthenticator $authenticator
     */
    public function __construct($server, BaseGoogleAuthenticator $authenticator)
    {
        $this->server = $server;
        $this->authenticator = $authenticator;
    }

    /**
     * Validates the code, which was entered by the user
     * @param $googleAuthCode
     * @param $code
     * @return bool
     */
    public function checkCode($googleAuthCode, $code)
    {
        return $this->authenticator->checkCode($googleAuthCode, $code);
    }

    /**
     * Generate the URL of a QR code, which can be scanned by Google Authenticator app
     * @param \AppBundle\Entity\User $user
     * @param $authCode
     * @return string
     */
    public function getUrl(User $user, $authCode)
    {
        return $this->authenticator->getUrl($user->getUsername(), $this->server, $authCode);
    }

    /**
     * Generate a new secret for Google Authenticator
     * @return string
     */
    public function generateSecret()
    {
        return $this->authenticator->generateSecret();
    }

    /**
     * Generates the attribute key for the session
     * @param TokenInterface|PostAuthenticationGuardToken $token
     * @return string
     */
    public function getSessionKey(TokenInterface $token)
    {
        return sprintf('acme_google_authenticator_%s_%s', $token->getProviderKey(), $token->getUsername());
    }

    /**
     * @param User|UserInterface $user
     * @return boolean
     */
    public function is2faActive(UserInterface $user)
    {
        return $user->getTwoFactorAuthentication();
    }
}