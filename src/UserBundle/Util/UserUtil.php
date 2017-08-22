<?php

namespace UserBundle\Util;

use Doctrine\ORM\EntityManager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UserBundle\Entity\User;

class UserUtil
{
    private $container;
    private $em;
    private $templating;
    private $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->templating = $container->get('templating');
        $this->mailer = $container->get('mailer');
    }

    public function apiReset(User $user)
    {
        // Create/set token.
        $token = bin2hex(openssl_random_pseudo_bytes(5));
        $user->setRecoverToken($token);

        // Create/set expires
        $expires = new \DateTime();
        $expires->modify('+1 hour');
        $user->setRecoverExpires($expires);

        $this->em->flush();

        // Send message.
        $message = \Swift_Message::newInstance()
            ->setSubject('Password Recovery')
            ->setFrom($this->container->getParameter('swiftmailer.sender_address'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->renderView(
                    'UserBundle:email:api_recover.html.twig',
                    ['token' => $token]
                ),
                'text/html'
            )
            ->addPart(
                $this->templating->renderView(
                    'UserBundle:email:api_recover.txt.twig',
                    ['token' => $token]
                ),
                'text/plain'
            )
        ;
        $this->mailer->send($message);
    }
}
