<?php

namespace UserBundle\Controller\Api;

use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/api/user", methods={"GET"})
     * @SWG\Response(
     *   response=200,
     *   description="Returns user info",
     * )
     * @SWG\Parameter(
     *   name="Authorization",
     *   in="header",
     *   required=true,
     *   type="string"
     * )
     * @SWG\Tag(name="user")
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $data['success'] = 1;
        $data['item'] = [
            'id' => $user->getId(),
            'created' => $user->getCreated(),
            'email' => $user->getEmail(),
            'name' => $user->getName()
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/user/recover/reset", methods={"GET"})
     * @SWG\Response(
     *   response=200,
     *   description="Triggers recover code user email",
     * )
     * @SWG\Parameter(
     *   name="Authorization",
     *   in="header",
     *   required=true,
     *   type="string"
     * )
     * @SWG\Tag(name="user")
     */
    public function resetAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $userUtil = $this->get('user.user_util');
        $userUtil.apiInitReset($user);

        return new JsonResponse(['success' => 1]);
    }

    /**
     * @Route("/api/user/recover", methods={"POST"})
     * @SWG\Response(
     *   response=200,
     *   description="Resets user password with code from email",
     * )
     * @SWG\Parameter(
     *   name="Authorization",
     *   in="header",
     *   required=true,
     *   type="string"
     * )
     * @SWG\Parameter(
     *   in="formData",
     *   name="code",
     *   description="The recover code sent via email",
     *   required=true,
     *   type="string"
     * )
     * @SWG\Tag(name="user")
     */
    public function recoverAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse(['success' => 1]);
    }
}
