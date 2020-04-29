<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/user/profile"), name="user_profile")
     */
    public function profile(Request $request)
    {
        $user = $request->getUser();
        return $this->render('user/profile.html.twig', [
            "user"=>$user
        ]);
    }

    /**
     * @Route("user/edit", name="user_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = $request->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $password = get('password')->getData;
            if (!empty($password)){
                $hashed=$encoder->encodePassword($user, $password);
                $user->setPassword($hashed);
                $em->persist($user);
                $em->flush();
            }

        }

        return $this->render('user/edit.html.twig', [
            "user"=>$user, "userForm"=>$userForm->createView()
        ]);
    }
}
