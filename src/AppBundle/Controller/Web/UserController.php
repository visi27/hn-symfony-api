<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/change_password", name="user_change_password")
     *
     * @param Request $request
     *
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        if ($request->getMethod() === 'POST') {
            $currentPassword = $request->get('piCurrPass');
            if ($this->get('security.password_encoder')->isPasswordValid($user, $currentPassword)) {
                if ($request->get('piNewPass') === $request->get('piNewPassRepeat')) {
                    $user->setPlainPassword($request->get('piNewPass'));

                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Fjalekalimi u ndryshuar me sukses');

                    return $this->render('account/dashboard_layout.html.twig');
                }
                $this->addFlash('error', 'Fushat Fjalekalimi i Ri dhe Perserit Fjalekalimin nuk jane njesoj!');
            } else {
                $this->addFlash('error', 'Fjalekalimi aktual qe jus shtypet nuk eshte i sakte!');
            }
        }

        return $this->render('account/change_password.html.twig');
    }

    /**
     * @Route("/dashboard", name="user_dashboard")
     *
     * @return Response
     *
     * @internal param Request $request
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function dashboardAction()
    {
        return $this->render('account/dashboard_layout.html.twig');
    }

    /**
     * @Route("/register", name="register_user")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /**
             * @var User
             */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome '.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render(
            'user/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
