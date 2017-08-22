<?php

namespace UserBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserAdminForm;

/**
 * @Route("/admin")
 */
class UserController extends Controller
{
    /**
     * @Route("/users", name="admin_user_list")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT u FROM UserBundle:User u";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'UserBundle:admin:list.html.twig', [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("/users/new", name="admin_user_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(UserAdminForm::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('User created.')
            );

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('UserBundle:admin:new.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="admin_user_edit")
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(UserAdminForm::class, $user);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('UserBundle:admin:edit.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/delete", name="admin_user_delete")
     */
    public function deleteAction(Request $request, User $user)
    {
        // Create empty form
        $data = [];
        $builder = $this->createFormBuilder($data);
        $form = $builder->getForm();

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'User deleted.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('UserBundle:admin:delete.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
