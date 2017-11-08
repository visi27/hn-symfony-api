<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Email already in use")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $defaultTwoFactorMethod = 'email';

    /**
     * @ORM\Column(type="boolean")
     */
    private $twoFactorAuthentication = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $twoFactorCode;

    /**
     * @var string Stores the secret code
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $googleAuthenticatorCode = null;

    /**
     * A non-persisted field that's used to create the encoded Google Auth Code.
     *
     * @var string
     */
    private $plainGoogleAuthenticatorCode;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @Assert\NotBlank(groups={"Registration"})
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * @param mixed $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTwoFactorMethod()
    {
        return $this->defaultTwoFactorMethod;
    }

    /**
     * @param string $defaultTwoFactorMethod
     *
     * @return User
     */
    public function setDefaultTwoFactorMethod($defaultTwoFactorMethod)
    {
        $this->defaultTwoFactorMethod = $defaultTwoFactorMethod;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTwoFactorAuthentication()
    {
        return $this->twoFactorAuthentication;
    }

    /**
     * @param bool $twoFactorAuthentication
     *
     * @return User
     */
    public function setTwoFactorAuthentication($twoFactorAuthentication)
    {
        $this->twoFactorAuthentication = $twoFactorAuthentication;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTwoFactorCode()
    {
        return $this->twoFactorCode;
    }

    /**
     * @param int $twoFactorCode
     *
     * @return User
     */
    public function setTwoFactorCode($twoFactorCode)
    {
        $this->twoFactorCode = $twoFactorCode;

        return $this;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getGoogleAuthenticatorCode()
    {
        return $this->googleAuthenticatorCode;
    }

    /**
     * @param string $googleAuthenticatorCode
     *
     * @return User
     * @codeCoverageIgnore
     */
    public function setGoogleAuthenticatorCode($googleAuthenticatorCode)
    {
        $this->googleAuthenticatorCode = $googleAuthenticatorCode;

        return $this;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getPlainGoogleAuthenticatorCode()
    {
        return $this->plainGoogleAuthenticatorCode;
    }

    /**
     * @param string $plainGoogleAuthenticatorCode
     *
     * @return User
     * @codeCoverageIgnore
     */
    public function setPlainGoogleAuthenticatorCode($plainGoogleAuthenticatorCode)
    {
        $this->plainGoogleAuthenticatorCode = $plainGoogleAuthenticatorCode;

        return $this;
    }
}
