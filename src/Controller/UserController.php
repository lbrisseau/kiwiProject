<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Notification\ContactNotification;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{

    ////////////////////////////////  ADMIN   ////////////////////////////////

    /**
     * @Route("/admin/user/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index_admin.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Encode the password
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new_admin.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show_admin.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $password = $encoder->encodePassword($user, $user->getPassword());
            // $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit_admin.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, SubscriptionRepository $subsRepo, EventRepository $eventRepo, ContactNotification $notification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Notification of user
            $notification->deletedAccount($user);
            // Notification of users affected by the removal of that user's subscriptions
            $allSubs = $subsRepo->findByUser($user, $eventRepo); //find all subscriptions of future events of that user
            foreach ($allSubs as $subs)
            {
                // Send a notification at each user affected by this, for each event
                $notification->waitingList($subs, $subsRepo);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    ////////////////////////////////  USER   ////////////////////////////////

    /**
     * @Route("/user/{id}", name="profil_show", methods={"GET"})
     */
    public function showProfil(User $user, SubscriptionRepository $subsRepo, EventRepository $eventRepo): Response
    {
        $adultDate = new DateTime();
        $adultDate->sub(new DateInterval('P18Y'));
        if ($user->getBirthDate() > $adultDate)
        {
        $currentEvent = $eventRepo->findNextKid()[0];
        }
        else
        {
        $currentEvent = $eventRepo->findNext()[0];
        }
        $isThereSubs = $subsRepo->findOne($user, $currentEvent);
        // dump($currentEvent);
        return $this->render('user/show.html.twig', [
            'isThereSubs' => $isThereSubs,
            'user' => $user,
            'event' => $currentEvent,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="profil_edit", methods={"GET","POST"})
     */
    public function editProfil(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $password = $encoder->encodePassword($user, $user->getPassword());
            // $user->setPassword($password);
            if ($this->isGranted('ROLE_MEMBER')) {
                $user->setRoles(['ROLE_MEMBER','ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            $this->getDoctrine()->getManager()->flush();

            // account edit confirmation message
            $this->addFlash('success', 'Vos modifications ont été enregistrées avec succès.');

            return $this->redirectToRoute('profil_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="profil_delete", methods={"POST"})
     */
    public function deleteProfil(Request $request, User $user, SubscriptionRepository $subsRepo, EventRepository $eventRepo, ContactNotification $notification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Notification of user
            $notification->deletedAccount($user);
            // Notification of users affected by the removal of that user's subscriptions
            $allSubs = $subsRepo->findByUser($user, $eventRepo); //find all subscriptions of future events of that user
            foreach ($allSubs as $subs)
            {
                // Send a notification at each user affected by this, for each event
                $notification->waitingList($subs, $subsRepo);
            }
            $this->container->get('security.token_storage')->setToken(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        // account deletion confirmation message
        $this->addFlash('warning', 'Votre compte utilisateur a bien été supprimé !'); 


        return $this->redirectToRoute('home');
    }

}

