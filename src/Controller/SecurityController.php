<?php

namespace App\Controller;

// use App\Entity\User;
// use DateTime;
// use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route(host="www.auribail-mx-park.local")
 */class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        // ajouter dans les paramètres : UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, 
        // $user = new User();
        // $user->setEmail("abcd@gmail.com")
        // ->setRoles(["ROLE_USER"])
        // ->setFirstName("Moi")
        // ->setLastName("fkel")
        // ->setBirthDate(new DateTime('now'))
        // ->setPhone("456");
        // $pass = $encoder->encodePassword($user, "abcd");
        // $user->setPassword($pass);
        // $manager->persist($user);
        // $manager->flush();

        if ($security->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('subscription_index');
        } elseif ($security->isGranted('ROLE_USER')){
            return $this->redirectToRoute('subscription_index');
        }

        // get the login error if there is one
        $error = $helper->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $helper->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
