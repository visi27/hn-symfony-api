<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Security\TwoFactor;

use AppBundle\Entity\User;

class HelperFactory
{
    /**
     * @var HelperInterface
     */
    private $emailHelper;
    /**
     * @var HelperInterface
     */
    private $googleHelper;

    public function __construct(HelperInterface $emailHelper, HelperInterface $googleHelper)
    {
        $this->emailHelper = $emailHelper;
        $this->googleHelper = $googleHelper;
    }

    public function getHelper(User $user)
    {
        switch ($user->getDefaultTwoFactorMethod()) {
            case 'email':
                return $this->emailHelper;
                break;
            case 'google':
                return $this->googleHelper;
                break;
            default:
                return $this->emailHelper;
        }
    }
}
