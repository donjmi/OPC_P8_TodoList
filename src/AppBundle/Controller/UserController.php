<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\UserAdminType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository('AppBundle:User')->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        // // Vérifier si l'utilisateur connecté a le rôle "ROLE_ADMIN"
        // $authorizationChecker = $this->get('security.authorization_checker');

        // if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
        //     $form = $this->createForm(UserAdminType::class, $user);
        // } else {
        //     $form = $this->createForm(UserType::class, $user);
        // }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
    * @Route("/users/{id}/edit", name="user_edit")
    */
    public function editAction(User $user, Request $request)
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // // Vérifier si l'utilisateur connecté a le rôle "ROLE_ADMIN"
        // $authorizationChecker = $this->get('security.authorization_checker');

        // if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
        //     $form = $this->createForm(UserAdminType::class, $user);
        // } else {
        //     $form = $this->createForm(UserType::class, $user);
        // }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
