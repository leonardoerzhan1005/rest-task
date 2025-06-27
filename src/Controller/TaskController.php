<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TaskController extends AbstractController
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    #[Route('/task', name: 'app_task')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TaskController.php',
        ]);
    }

    #[Route('/api/tasks', name: 'get_tasks', methods: ['GET'])]
    public function getTasks(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;

        $qb = $this->em->getRepository(Task::class)->createQueryBuilder('t');
        if ($status) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status);
        }
        $qb->setFirstResult($offset)->setMaxResults($limit);
        $tasks = $qb->getQuery()->getResult();

        // Для подсчёта общего количества
        $countQb = $this->em->getRepository(Task::class)->createQueryBuilder('t');
        if ($status) {
            $countQb->andWhere('t.status = :status')->setParameter('status', $status);
        }
        $total = (int) $countQb->select('COUNT(t.id)')->getQuery()->getSingleScalarResult();

        $data = array_map(fn($task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
        ], $tasks);

        return $this->json([
            'tasks' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil($total / $limit),
            ],
        ]);
    }

    #[Route('/api/tasks/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(int $id): JsonResponse
    {
        $task = $this->em->getRepository(Task::class)->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
        ]);
    }

    #[Route('/api/tasks', name: 'create_task', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDescription($data['description'] ?? null);
        $task->setStatus($data['status'] ?? 'new');
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }
        $this->em->persist($task);
        $this->em->flush();
        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PUT'])]
    public function updateTask(int $id, Request $request): JsonResponse
    {
        $task = $this->em->getRepository(Task::class)->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['title'])) $task->setTitle($data['title']);
        if (array_key_exists('description', $data)) $task->setDescription($data['description']);
        if (isset($data['status'])) $task->setStatus($data['status']);
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }
        $this->em->flush();
        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
        ]);
    }

    #[Route('/api/tasks/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(int $id): JsonResponse
    {
        $task = $this->em->getRepository(Task::class)->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
        $this->em->remove($task);
        $this->em->flush();
        return $this->json(['message' => 'Task deleted']);
    }
}
