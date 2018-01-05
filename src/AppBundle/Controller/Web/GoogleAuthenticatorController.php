<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use AppBundle\Security\Encryption\EncryptionService;
use AppBundle\Security\TwoFactor\Google\Helper as GoogleHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GoogleAuthenticatorController.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class GoogleAuthenticatorController extends Controller
{
    /**
     * @Route("/_otp/activate", name="auth_activate_otp")
     *
     * @param Request $request
     *
     * @param GoogleHelper $googleHelper
     * @param EncryptionService $encryptionService
     *
     * @return RedirectResponse|Response
     */
    public function activateGoogleAuthenticatorAction(
        Request $request,
        GoogleHelper $googleHelper,
        EncryptionService $encryptionService
    ) {
        /**
         * @var User
         */
        $user = $this->getUser();
        if (!$user) {
            return new Response('You need to login');
        }

        if ($user->getTwoFactorAuthentication()) {
            //Flag authentication complete
            $this->addFlash('error', 'Google Authenticator Is Already Active');

            return $this->redirectToRoute('user_dashboard');
        }

        if ($request->getMethod() === 'POST') {
            //check authentication key
            //Check the authentication code
            $authKey = $encryptionService->decrypt($user->getGoogleAuthenticatorCode());
            if ($googleHelper->checkCode($authKey, $request->get('_auth_code')) === true) {
                //activate 2fa authenticator
                $user->setTwoFactorAuthentication(true);
                $user->setDefaultTwoFactorMethod('google');

                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Google Authenticator Activated');

                return $this->redirectToRoute('user_dashboard');
            }
            $this->addFlash('error', 'Invalid Code');
            $qrCodeUrl = $googleHelper->getUrl($user, $authKey);

            return $this->render('twofactor/activate.html.twig', ['qrCodeURL' => $qrCodeUrl]);
        }
        //generate new key
        $code = $googleHelper->generateSecret();

        $user->setGoogleAuthenticatorCode('');
        $user->setPlainGoogleAuthenticatorCode($code);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        $qrCodeUrl = $googleHelper->getUrl($user, $code);

        return $this->render('twofactor/activate.html.twig', ['qrCodeURL' => $qrCodeUrl]);
    }
}
