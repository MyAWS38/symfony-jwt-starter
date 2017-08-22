<?php

namespace UserBundle\Controller\Api;

use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends Controller
{
    /**
     * @Route("/api/tokens", methods={"POST"})
     * @SWG\Response(
     *   response=200,
     *   description="Returns user token when valid basic auth is sent",
     * )
     * @SWG\Tag(name="tokens")
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
     * @Route("/api/tokens/check", methods={"GET"})
     * @SWG\Response(
     *   response=200,
     *   description="Returns successful if user is valid",
     * )
     * @SWG\Parameter(
     *   name="Authorization",
     *   in="header",
     *   required=true,
     *   type="string"
     * )
     * @SWG\Tag(name="tokens")
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
