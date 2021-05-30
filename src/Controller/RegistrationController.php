<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(RegistrationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $user = new User();
            $user->setFirstName($form->get('firstName')->getData());
            $user->setLastName($form->get('lastName')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setBirthDate($form->get('birthDate')->getData());
            $user->setPhone($form->get('phone')->getData());
            $user->setLicenceNumber($form->get('licenceNumber')->getData());
            $user->setRoles($form->get('roles')->getData());
            $user->setPassword($form->get('password')->getData());

            $em->persist($user);
            $em->flush();

            die('Redirection page accueil');
        }

        return $this->render('registration/registration.html.twig', [
            'regForm' => $form->createView()
        ]);
    }
}
