<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Subscription;
use App\Form\ContactType;
use App\Form\SubscriptionType;
use App\Notification\ContactNotification;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(host="www.auribail-mx-park.local")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", defaults={"_fragment"="accueil"}, requirements={"_fragment": "accueil|club|evenement|contact"})
     */
    public function index(Request $request, ContactNotification $notification, SubscriptionRepository $subsRepo, EventRepository $eventRepo, EntityManagerInterface $manager): Response
    {
        // Contact form management:
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        if ($formContact->isSubmitted() && $formContact->isValid())
        {
            $notification->notify($contact);
            $this->addFlash('success', "Votre message a bien été envoyé.");
            return $this->redirectToRoute("home", ['_fragment' => 'contact']);
        }
        $currentEvent = null;
        $isThereSubs = null;
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $user = $this->getUser();
            $adultDate = new DateTime('-18Y');
            if ($user->getBirthDate() > $adultDate)
            {
                $currentEvent = $eventRepo->findNextKid()[0];
            }
            else
            {
                $currentEvent = $eventRepo->findNext()[0];
            }
            $isThereSubs = $subsRepo->findOne($user, $currentEvent);
        }
        // Normal rendering:
        return $this->render('home/index.html.twig', [
            'formContact' => $formContact->createView(),
            'event' => $eventRepo->findNext(),
            'kids' => $eventRepo->findNextKid(),
            'currentEvent' => $currentEvent,
            'isThereSubs' => $isThereSubs
        ]);
    }

    /**
     * @Route("/subs", name="subscriptionManager")
     */
    public function subscriptionManager(Request $request, EventRepository $eventRepo, EntityManagerInterface $manager)
    {
        // New subscription management:
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
        $adultDate = new DateTime('-18Y');
        if ($user->getBirthDate() > $adultDate)
        {
            $event = $eventRepo->findNextKid()[0];
        }
        else
        {
            $event = $eventRepo->findNext()[0];
        }
        $subs = new Subscription();
        $formSubs = $this->createForm(SubscriptionType::class, $subs);
        $formSubs->handleRequest($request);
        $subs->setSubsDate(new DateTime());
        $subs->setValidationState(!is_null($user->getLicenceNumber()));
        $subs->setEvent($event);
        $subs->setUser($user);
        $manager->persist($subs);
        $manager->flush();
        $this->addFlash('success', "Vous êtes désormais inscrit.e à l'événement.");
        // else
        // 
        //     $this->addFlash('success', "Vous êtes déjà inscrit.e à l'événement.");
        // }
        return $this->redirectToRoute("home");
    }
    
    /**
     * @Route("/unsubs", name="unsubscriptionManager")
     */
    public function unsubscriptionManager(Request $request, EventRepository $eventRepo, SubscriptionRepository $subsRepo, EntityManagerInterface $manager)
    {
        // New subscription management:
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
        $adultDate = new DateTime('-18Y');
        if ($user->getBirthDate() > $adultDate)
        {
            $event = $eventRepo->findNextKid()[0];
        }
        else
        {
            $event = $eventRepo->findNext()[0];
        }
        $subs = $subsRepo->findOne($user, $event);
        // if ($this->isCsrfTokenValid('delete' . $subs->getId(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($subs);
        $entityManager->flush();
        $this->addFlash('success', "Vous êtes désinscrit.e de l'événement.");
        return $this->redirectToRoute("home");
        // DELETE FROM `subscription` WHERE `subscription`.`id` = 102;
    }
}
