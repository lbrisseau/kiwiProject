<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Subscription;
use App\Form\ContactType;
use App\Form\SubscriptionType;
use App\Notification\ContactNotification;
use App\Repository\EventRepository;
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
    public function subscriptionManager(Request $request, EventRepository $eventRepo, EntityManagerInterface $manager)
    {
        // New subscription management:
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $subs = new Subscription();
        $formSubs = $this->createForm(SubscriptionType::class, $subs);
        $formSubs->handleRequest($request);
        $subs->setSubsDate(new DateTime());
        $subs->setValidationState(!is_null($user->getLicenceNumber()));
        $adultDate = new DateTime('-18Y');
        // var_dump($subs);
        // exit;
        if ($user->getBirthDate() > $adultDate)
        {
            $subs->setEvent($eventRepo->findNextKid()[0]);
        }
        else
        {
            $subs->setEvent($eventRepo->findNext()[0]);
        }
        $subs->setUser($user);
        $manager->persist($subs);
        $manager->flush();
        $this->addFlash('success', "Vous êtes inscrit.e à l'événement.");
        return $this->redirectToRoute("home");
    }
}
