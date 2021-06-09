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
    public function index(Request $request, ContactNotification $notification, EventRepository $eventRepo, EntityManagerInterface $manager): Response
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
        // Normal rendering:
        return $this->render('home/index.html.twig', [
            'formContact' => $formContact->createView(),
            'event' => $eventRepo->findNext(),
            'kids' => $eventRepo->findNextKid()
        ]);
    }

    /**
     * @Route("/subs", name="subscriptionManager")
     */
    public function subscriptionManager(Request $request, EventRepository $eventRepo, SubscriptionRepository $subsRepo, EntityManagerInterface $manager)
    {
        // New subscription management:
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
        $isThereSubs = $subsRepo->findOne($user, $event);
        if (is_null($isThereSubs))
        {
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
        }
        else
        {
            $this->addFlash('success', "Vous êtes déjà inscrit.e à l'événement.");
        }
        return $this->redirectToRoute("home");
    }
    // DELETE FROM `subscription` WHERE `subscription`.`id` = 102;
}
