<?php

namespace UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UserBundle\Entity\User;
use UserBundle\Form\RecoverFormType;
use UserBundle\Form\RecoverResetFormType;
use UserBundle\Form\RegisterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterFormType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome ' . $user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('UserBundle:user:register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/recover", name="user_recover")
     */
    public function recoverAction(Request $request)
    {
        $form = $this->createForm(RecoverFormType::class);

        $form->handleRequest($request);
        if ($request->getMethod() === 'POST' && $form->isValid()) {

            $data = $form->getData();
            $email = $data['email'];

            $em = $this->getDoctrine()->getManager();

            /* @var User $user */
            $user = $em->getRepository('UserBundle:User')
                ->findOneBy(['email' => $email]);

            if (isset($user)) {
                // Create/set token.
                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $user->setRecoverToken($token);

                // Create/set expires
                $expires = new \DateTime();
                $expires->modify('+1 hour');
                $user->setRecoverExpires($expires);

                $em->flush();

                // Generate recovery URL.
                $recover_url = $this->generateUrl(
                    'user_recover_reset',
                    [
                        'id' => $user->getId(),
                        'token' => $user->getRecoverToken()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                // Send message.
                $message = \Swift_Message::newInstance()
                    ->setSubject('Password Recovery')
                    ->setFrom($this->getParameter('swiftmailer.sender_address'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'UserBundle:email:recover.html.twig',
                            ['recover_url' => $recover_url]
                        ),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView(
                            'UserBundle:email:recover.txt.twig',
                            ['recover_url' => $recover_url]
                        ),
                        'text/plain'
                    )
                ;
                $this->get('mailer')->send($message);
            }

            $this->addFlash('success', 'Recovery instructions have been sent to ' . $email . ', if this account exists.');
        }

        return $this->render('UserBundle:user:recover.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/recover/reset", name="user_recover_reset")
     */
    public function recoverResetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')
            ->findOneBy([
                'id' => $request->query->get('id'),
                'recoverToken' => $request->query->get('token')
            ]);

        // If there's no matching id and token, then redirect to login.
        $now = new \DateTime();
        if (!isset($user)) {
            $this->addFlash('warning', 'Invalid recovery credentials.');
            return $this->redirectToRoute('security_login');
        }
        // Redirect if the token is valid, but expired.
        elseif ($now >= $user->getRecoverExpires()) {
            $this->addFlash('warning', 'This recovery link has been used or is expired.');
            return $this->redirectToRoute('security_login');
        }

        // Handle successful recovery case.
        $form = $this->createForm(RecoverResetFormType::class);

        $form->handleRequest($request);
        if ($request->getMethod() === 'POST') {
            $data = $form->getData();
            $pass = isset($data['plainPassword']) ? $data['plainPassword'] : NULL;

            // Generate error based on password length.
            if (strlen($pass) < 6) {
                $form->get('plainPassword')
                    ->addError(new FormError('Password must be at least 6 characters in length.'));
            }

            if ($form->isValid()) {
                // Expire recovery token and save password.
                $user->setRecoverExpires($now);
                $user->setPlainPassword($pass);
                $em->flush();

                $this->addFlash('success', 'Password reset successfully.');
                return $this->redirectToRoute('security_login');
            }
        }

        return $this->render('UserBundle:user:recover_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
