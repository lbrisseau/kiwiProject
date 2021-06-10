<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Form\EventType;
use App\Repository\EventRepository;
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
    public function new(Request $request): Response
    {
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
        ]);
    }

    /**
     * @Route("/admin/event/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event, SubscriptionRepository $sub, Request $request): Response
    {
        $param = $request->query->get('from');
        //var_dump($param);
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
        var_dump($param);


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
     * @Route("/admin/event/{id}/validate", name="event_validate", methods={"POST"})
     */
    public function validate(Request $request, Event $event)
    {
        if ($this->isCsrfTokenValid('validate' . $event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $tabSubs = $event->getSubscriptions();
            //var_dump($tabSubs);
            foreach ($tabSubs as $sub) {
                if ($sub->getValidationState() == false) {
                    $entityManager->remove($sub);
                    $entityManager->flush();
                }
            }
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

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->MultiCell(150, 6, "Liste ".$event->getName());
            $pdf->Ln(10);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 12);
            foreach ($tabUsers as $key => $personne) {

                if ($key == 50)
                    $pdf->SetXY(100, 25);
                if ($key >= 50)
                    $decalage = true;
                else
                    $decalage = false;

                if ($key & 1) {
                    if ($decalage == true)
                        $pdf->Cell(100);
                    $pdf->Cell(25);
                    $pdf->SetFillColor(128, 128, 128);
                    $pdf->Cell(25, 5, addslashes($personne->getFirstName()), 0, 0, 'L', true);
                    $pdf->Cell(25, 5, addslashes($personne->getLastName()), 0, 1, 'C', true);
                } else {
                    if ($decalage == true)
                        $pdf->Cell(100);
                    $pdf->Cell(25);
                    $pdf->Cell(25, 5, addslashes($personne->getFirstName()), 0, 0, 'L');
                    $pdf->Cell(25, 5, addslashes($personne->getLastName()), 0, 1, 'C');
                }
            }

            return new Response($pdf->Output("MX Trail - Liste Session ".addslashes($event->getName()).".pdf","D"), 200, array('Content-Type' => 'application/pdf'));
        }
    }
}
