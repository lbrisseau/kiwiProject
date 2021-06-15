<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\SubscriptionType;
use App\Notification\ContactNotification;
use App\Repository\AnimationRepository;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use DateInterval;
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
    public function index(Request $request, ContactNotification $notification, SubscriptionRepository $subsRepo, EventRepository $eventRepo, AnimationRepository $animationRepo): Response
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
        else if ($formContact->isSubmitted())
        {
            $this->addFlash('warning', "Le message n'a pas été envoyé.");
        }
        // Initialization of various dates + event
        $currentEvent = null;
        $isThereSubs = null;
        $dateEvent = null;
        $dateStartMember = null;
        $dateStartAll = null;
        $dateEnd = null;
        $securityContext = $this->container->get('security.authorization_checker');
        // Hydration of those variables in case the user is logged in
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $user = $this->getUser();
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
            $dateEvent = clone $currentEvent->getDate();
            $dateStartMember = clone $currentEvent->getDate();
            $dateStartAll = clone $currentEvent->getDate();
            $dateEnd = clone $currentEvent->getDate();
            $dateStartMember->sub(new DateInterval('P'.$currentEvent->getStartMemberSubs().'D'));
            $dateStartAll->sub(new DateInterval('P'.$currentEvent->getStartAllSubs().'D'));
            $dateEnd->sub(new DateInterval('P'.$currentEvent->getEndSubs().'D'));
        }
        // Normal rendering:
        return $this->render('home/index.html.twig', [
            'formContact' => $formContact->createView(),
            'event' => $eventRepo->findNext(),
            'kids' => $eventRepo->findNextKid(),
            'currentEvent' => $currentEvent,
            'isThereSubs' => $isThereSubs,
            'dateEvent' => $dateEvent,
            'dateStartMember' => $dateStartMember,
            'dateStartAll' => $dateStartAll,
            'dateEnd' => $dateEnd,
            'animation' => $animationRepo->findFirst()
        ]);
    }

    /**
     * @Route("/subs/{event}-{user}", name="subscriptionManager", methods={"POST"})
     */
    public function subscriptionManager(Request $request, ContactNotification $notification, SubscriptionRepository $subsRepo, EntityManagerInterface $manager, Event $event, User $user)
    {
        if ($this->isCsrfTokenValid('subs' . $event->getId() . $user->getId(), $request->request->get('_token'))) {
            // Creation of the subscription if none existed
            $check = $subsRepo->findOne($user, $event);
            if (is_null($check))
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
            }
            $this->addFlash('success', "Vous êtes désormais inscrit.e à l'événement.");
            // Email notification of the user
            $allSubsBefore = $subsRepo->countOrder($user, $event);
            if ($event->getType() == true) // if it is a normal event
            {
                $waitingList = max(-1,$allSubsBefore[1] - 75);
            }
            else // if it is a kid event
            {
                $waitingList = max(-1,$allSubsBefore[1] - 15);
            }
            $notification->subscription($user, $event->getName(), $event->getDate(), $waitingList);
        }
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/unsubs/{id}", name="unsubscriptionManager", methods={"POST"})
     */
    public function unsubscriptionManager(Request $request, ContactNotification $notification, Subscription $subs, SubscriptionRepository $subsRepo)
    {
        if ($this->isCsrfTokenValid('unsubs' . $subs->getId(), $request->request->get('_token'))) {
            // Email notification of the user
            $notification->unsubscription($subs);
            // Notification of users affected by the removal
            $notification->waitingList($subs, $subsRepo);
            // Removal of subscription
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subs);
            $entityManager->flush();
            // Little flash message of success
            $this->addFlash('success', "Vous êtes désinscrit.e de l'événement.");
        }
        return $this->redirectToRoute("home");
    }
}
