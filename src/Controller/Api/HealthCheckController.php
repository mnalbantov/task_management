<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Response\Error\ViolationError;
use App\Response\Error\ViolationResponseHandlerInterface;
use App\Response\ErrorResponse;
use App\Response\SuccessResponse;
use App\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HealthCheckController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private ViolationResponseHandlerInterface $violationResponseHandler;
    private SerializerInterface $serializer;
    private ProjectService $service;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ViolationResponseHandlerInterface $violationResponseHandler,
        ProjectService $service
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->violationResponseHandler = $violationResponseHandler;
        $this->service = $service;
    }

    #[Route('/health-check', name: 'app_health_check')]
    public function index(): JsonResponse
    {
        $this->entityManager->getConnection()->connect();

        return $this->json([
            'message' => 'The app is configured & working properly!',
            'DB' => $this->entityManager->getConnection()->getDatabase(),
        ]);
    }

    #[Route('/test', name: 'test', methods: ['POST'])]
    public function test(Request $request): JsonResponse
    {
        $project = $this->serializer->deserialize(
            $request->getContent(),
            Project::class,
            'json'
        );
        $violations = $this->validator->validate($project);
        if (count($violations) > 0) {
            return $this->violationResponseHandler
                ->handleViolationResponse(
                    new ViolationError($violations)
                );
        }
        $this->service->createProject($project);

        return new SuccessResponse();
    }
}