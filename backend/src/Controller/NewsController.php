<?php

namespace App\Controller;

use App\Http\CacheableJsonResponseTrait;
use App\Http\RequestValidationTrait;
use App\Repository\ContentRepositoryInterface;
use App\Security\CsrfProtectedTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsController extends AbstractController
{
    use CsrfProtectedTrait;
    use RequestValidationTrait;
    use CacheableJsonResponseTrait;

    public function __construct(
        #[Autowire(service: 'app.repository.news')]
        private readonly ContentRepositoryInterface $repository,
    ) {
    }

    #[Route('/api/read_config', name: 'readConfig', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->jsonWithCaching($request, ['version' => 2, 'news' => $this->repository->findAll()]);
    }

    #[Route('/api/add_news', name: 'addNews', methods: ['POST'])]
    public function add(Request $request, LoggerInterface $logger): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $fields = $this->requireFields($request, ['headline', 'content', 'url', 'image']);

        try {
            $id = $this->repository->add($fields);
        } catch (\RuntimeException $e) {
            $logger->error('Failed to save news: '.$e->getMessage());

            return new JsonResponse(['status' => 'error', 'message' => 'Failed to save news.'], 500);
        }

        return new JsonResponse(['status' => 'ok', 'id' => $id]);
    }

    #[Route('/api/edit_news', name: 'editNews', methods: ['POST'])]
    public function edit(Request $request, LoggerInterface $logger): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $fields = $this->requireFields($request, ['headline', 'content', 'url', 'image', 'uniqueId']);
        $id = $fields['uniqueId'];
        unset($fields['uniqueId']);

        try {
            $this->repository->update($id, $fields);
        } catch (\RuntimeException $e) {
            $logger->error('Failed to update news: '.$e->getMessage());

            return new JsonResponse(['status' => 'error', 'message' => 'Failed to update news.'], 500);
        }

        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/api/remove_news', name: 'removeNews', methods: ['POST'])]
    public function remove(Request $request, LoggerInterface $logger): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $fields = $this->requireFields($request, ['id']);

        try {
            $this->repository->remove($fields['id']);
        } catch (\RuntimeException $e) {
            $logger->error('Failed to remove news: '.$e->getMessage());

            return new JsonResponse(['status' => 'error', 'message' => 'Failed to remove news.'], 500);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
