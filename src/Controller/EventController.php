<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Subscription;
use App\Form\ContactType;
use App\Form\EventType;
use App\Notification\ContactNotification;
use App\Repository\EventRepository;
use App\Repository\SettingsRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Fpdf\Fpdf;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EventController extends AbstractController
{
    /**
     * @Route("/admin/event/", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index_admin.html.twig', [
            'events' => $eventRepository->findAllByDateDesc(),
        ]);
    }

    /**
     * @Route("/admin/event/new", name="event_new", methods={"GET","POST"})
     */
    public function new(Request $request, SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findFirst();
        
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new_admin.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    /**
     * @Route("/admin/event/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event, SubscriptionRepository $sub, Request $request): Response
    {
        $param = $request->query->get('from');
        if ($param == 'admin') {
            return $this->render('event/show_admin.html.twig', [
                'event' => $event,
                'users' => $sub->findUsers($event),
                'origin' => 'admin'
            ]);
        } else {
            return $this->render('event/show_admin.html.twig', [
                'event' => $event,
                'users' => $sub->findUsers($event),
            ]);
        }
    }

    /**
     * @Route("/admin/event/{id}/edit", name="event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $event): Response
    {
        $param = $request->query->get('from');
        //var_dump($param);


        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if ($param == 'admin') {
                return $this->redirectToRoute('admin');
            } else {
                return $this->redirectToRoute('event_index');
            }
        }


        if ($param == 'admin') {
            return $this->render('event/edit_admin.html.twig', [
                'event' => $event,
                'form' => $form->createView(),
                'origin' => 'admin'
            ]);
        } else {
            return $this->render('event/edit_admin.html.twig', [
                'event' => $event,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/admin/event/{id}", name="event_delete", methods={"POST"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * @Route("/admin/event/{id}/announcement", name="event_preannouncement")
     */
    public function preannouncement(Event $event)
    {
        return $this->render('event/preannouncement_admin.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/admin/event/{id}/{element}/announcement", name="event_announcement")
     */
    public function announcement(Request $request, Event $event, $element, ContactNotification $notification)
    {
        // Contact form management:
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        if ($formContact->isSubmitted())
        {
            $subject = $contact->getSubject();
            $message = $contact->getMessage();
            if (is_null($message))
            {
                if ($element == '0')
                {
                    $message = "Bonjour,\n\nÀ cause des intempéries, l'événement '".
                    $event->getName()."' a été reporté au ".
                    $event->getDate()->format('d/m/Y').
                    ".\nN'oubliez pas de vous désinscrire sur notre site si jamais vous n'étiez pas disponible à cette date.
                    \n\nÀ très vite !\n\nL'équipe de Auribail MX Park.";
                }
                else if ($element == '1')
                {
                    $message = "Bonjour,\n\nÀ cause des intempéries, l'événement '".
                    $event->getName()."' a été annulé.\n\nNous espérons vous revoir très vite !
                    \n\nL'équipe de Auribail MX Park.";
                }
                else
                {
                    $message = "Bonjour,\n\nCeci est une mauvaise manip de la part de notre amour d'admin.
                    \n\nMerci de votre compréhension,\n\nL'équipe de Auribail MX Park.";
                }
            }
            $notification->cancelledEvent($event, $subject, $message);
            return $this->redirectToRoute("event_show", ['id' => $event->getId()]);
        }
        return $this->render('event/announcement_admin.html.twig', [
            'event' => $event,
            'element' => $element,
            'form' => $formContact->createView(),
        ]);
    }

    /**
     * @Route("/admin/event/{id}/checklicence", name="event_check_licence")
     */
    public function checkLicence(Event $event, ContactNotification $notification, SubscriptionRepository $repo)
    {
        $notification->checkLicence($event, $repo);
        return $this->redirectToRoute('event_show', [
            'id' => $event->getId(),
        ]);
    }

    /**
     * @Route("/admin/event/{id}/validate", name="event_validate", methods={"POST"})
     */
    public function validate(Request $request, Event $event, ContactNotification $notification)
    {
        if ($this->isCsrfTokenValid('validate' . $event->getId(), $request->request->get('_token'))) {
            $notification->finalSubs($event);
            $entityManager = $this->getDoctrine()->getManager();
            $tabSubs = $event->getSubscriptions();
            //var_dump($tabSubs);
            foreach ($tabSubs as $sub) {
                if ($sub->getValidationState() == false) {
                    $entityManager->remove($sub);
                    $entityManager->flush();
                }
            }
            $notification->finalListAdmin($event);
        }
        return $this->redirectToRoute('event_show', [
            'id' => $event->getId(),
        ]);
    }

    /**
     * @Route("/admin/event/{id}/pdf", name="event_createpdf", methods={"POST"})
     */
    public function createPdf(Request $request, SubscriptionRepository $sub, Event $event)
    {
        if ($this->isCsrfTokenValid('pdf' . $event->getId(), $request->request->get('_token'))) {

            // Création d'un tableau de personnes
            $tabSubs = $event->getSubscriptions();
            $tabUsers = [];

            foreach ($tabSubs as $sub) {
                $user = $sub->getUser();
                $tabUsers[] = $user;
            }
            // Création d'un PDF
            $pdf = new Fpdf();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetLeftMargin(0);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 12);
            // Move to the right
            $pdf->Cell(80);
            // Title
            $pdf->Cell(30,10,"Liste ".utf8_decode($event->getName()),0,0,'C');
            // Line break
            $pdf->Ln(20);
            $w = array(55, 50, 55, 50);
            $fill = true;
            $line = false;
            foreach ($tabUsers as $key => $personne)
            {
                if ($fill) {$pdf->SetFillColor(229,229,229);}
        	    else {$pdf->SetFillColor(246,246,246);}
        	    // echo($i);
        	    if ($line)
        	    {
    		    	$pdf->Cell($w[2],10, ($key+1)." : ".utf8_decode($personne->getFirstName())." ".utf8_decode($personne->getLastName()),1,0,'L',1);
    		    	$pdf->Cell($w[3],10, "",1,0,'L',1);
    		    	$pdf->Ln();
        	    }
        	    else
        	    {
        	    	$pdf->Cell($w[0],10, ($key+1)." : ".utf8_decode($personne->getFirstName())." ".utf8_decode($personne->getLastName()),1,0,'L',1);
    		    	$pdf->Cell($w[1],10, "",1,0,'L',1);
    		    	$fill = !$fill;
        	    }
        	    $line = !$line;
            }
            // Closing line
            $pdf->Cell(array_sum($w),0,'','T');
            return new Response($pdf->Output("MX Trail - Liste Session ".utf8_decode($event->getName()).".pdf","D"), 200, array('Content-Type' => 'application/pdf'));
        }
    }
}