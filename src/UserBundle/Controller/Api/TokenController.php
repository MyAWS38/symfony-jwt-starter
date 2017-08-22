<?php

namespace UserBundle\Controller\Api;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Route("/api")
 */
class TokenController extends Controller
{
    /**
     * @Route("/tokens")
     * @Method("POST")
     * @ApiDoc(
     *   resource=false,
     *   description="Basic authentication endpoint.",
     *   statusCodes={
     *     200="Returned when successful"
     *   }
     * )
     */
    public function newTokenAction(Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository('UserBundle:User')
            ->findOneBy(['email' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->getPassword());

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'email' => $user->getEmail(),
                'exp' => time() + 86400
            ]);

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/tokens/check")
     * @Method("GET")
     * @ApiDoc(
     *   resource=false,
     *   description="Checks if token is valid.",
     *   headers={
     *     {
     *       "name"="Authorization",
     *       "required"=true,
     *       "description"="Authorization key"
     *     }
     *   },
     *   statusCodes={
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized"
     *   }
     * )
     */
    public function checkTokenAction(Request $request)
    {
        $data = [
            'success' => 0,
            'message' => 'Invalid token'
        ];

        $user = $this->getUser();
        if ($user) {
            $data['success'] = 1;
            $data['message'] = 'Valid token';
        }

        return new JsonResponse($data);
    }
}
