<?php

namespace Tests\AppBundle\Security\TwoFactor;

use AppBundle\Entity\User;
use AppBundle\Security\TwoFactor\Email\Helper as EmailHelper;
use AppBundle\Security\TwoFactor\Google\Helper as GoogleHelper;
use AppBundle\Security\TwoFactor\HelperFactory;

use AppBundle\Test\ContainerDependableTestCase;


class HelperFactoryTest extends ContainerDependableTestCase
{
    public function testHelperFactory(){
        $emailHelper = new EmailHelper($this->get("doctrine")->getManager(), $this->get("swiftmailer.mailer"));
        $googleHelper = new GoogleHelper($this->get("doctrine")->getManager(), $this->get("app.security.twofactor.google"));
        $factory = new HelperFactory($emailHelper, $googleHelper);

        $user = new User();

        //Whene default is not specified in User than use EmailHelper
        $user->setDefaultTwoFactorMethod("");
        $this->assertInstanceOf("AppBundle\Security\TwoFactor\Email\Helper", $factory->getHelper($user));

        $user->setDefaultTwoFactorMethod("email");
        $this->assertInstanceOf("AppBundle\Security\TwoFactor\Email\Helper", $factory->getHelper($user));

        $user->setDefaultTwoFactorMethod("google");
        $this->assertInstanceOf("AppBundle\Security\TwoFactor\Google\Helper", $factory->getHelper($user));
    }
}
