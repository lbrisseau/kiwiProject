<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
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
    public function index(Request $request, ContactNotification $notification): Response
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
            'formContact' => $formContact->createView()
        ]);
    }
}
