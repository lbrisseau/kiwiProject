<?php

namespace App\Controller;


use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'subscriptions' => $subscriptionRepository->findAll(),
        ]);
    }
}
