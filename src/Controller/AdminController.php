<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\EventRepository;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'events' => $eventRepository->findTwoNext(),
        ]);
    }

    /**
     * @Route("/admin/settings", name="settings")
     */
    public function settings(Request $request, SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findFirst();
        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->render('admin/settings.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
