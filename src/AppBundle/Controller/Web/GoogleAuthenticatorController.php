<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use AppBundle\Security\TwoFactor\Google\Helper;
use Google\Authenticator\GoogleAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GoogleAuthenticatorController
 * @package AppBundle\Controller\Web
 * @Security("is_granted('ROLE_USER')")
 */
class GoogleAuthenticatorController extends Controller
{
    /**
     * @Route("/_otp/activate", name="auth_activate_otp")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function activateGoogleAuthenticatorAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            return new Response("You need to login");
        }

        if ($user->getTwoFactorAuthentication()) {
            //Flag authentication complete
            $this->addFlash('error', 'Google Authenticator Is Already Active');

            return $this->redirectToRoute('admin_blog_list');
        }

        $authenticator = new GoogleAuthenticator();
        $helper = new Helper("TestServer", $authenticator);

        if ($request->getMethod() == 'POST') {
            //check authentication key
            //Check the authentication code
            if ($helper->checkCode($user, $request->get('_auth_code')) == true) {
                //activate 2fa authenticator
                $user->setTwoFactorAuthentication(true);
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Google Authenticator Activated');

                return $this->redirectToRoute('admin_blog_list');
            } else {
                $this->addFlash('error', 'Invalid Code');
                $qrCodeUrl = $helper->getUrl($user);

                return $this->render('twofactor/activate.html.twig', ['qrCodeURL' => $qrCodeUrl]);
            }
        } else {
            //generate new key
            $code = $helper->generateSecret();
            $user->setGoogleAuthenticatorCode($code);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $qrCodeUrl = $helper->getUrl($user);

            return $this->render('twofactor/activate.html.twig', ['qrCodeURL' => $qrCodeUrl]);
        }

    }
}