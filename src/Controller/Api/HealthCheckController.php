<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/health-check', name: 'app_health_check')]
    public function index(): JsonResponse
    {
        try {
            $this->entityManager->getConnection()->connect();

            return $this->json([
                'message' => 'The app is configured & working properly!',
                'DB' => $this->entityManager->getConnection()->getDatabase(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}