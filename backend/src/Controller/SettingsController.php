<?php

namespace App\Controller;

use App\Http\CacheableJsonResponseTrait;
use App\Http\RequestValidationTrait;
use App\Repository\SettingsRepository;
use App\Security\CsrfProtectedTrait;
use App\Security\Roles;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingsController extends AbstractController
{
    use CsrfProtectedTrait;
    use RequestValidationTrait;
    use CacheableJsonResponseTrait;

    public function __construct(
        private readonly SettingsRepository $repository,
    ) {
    }

    #[Route('/api/read_settings', name: 'readSettings', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->jsonWithCaching($request, $this->repository->load());
    }

    #[Route('/api/save_settings', name: 'saveSettings', methods: ['POST'])]
    public function save(Request $request, LoggerInterface $logger): JsonResponse
    {
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN);
        $this->assertValidCsrfToken($request);
        $fields = $this->requireFields($request, [
            'basicReload', 'newsNumber', 'tickerSpeed', 'layoutOrientation',
            'tickerOrientation', 'tickerHeight', 'showTicker', 'slideDelay',
        ]);

        $newSettings = [
            'generalReload' => (int) $fields['basicReload'],
            'newsNumber' => (int) $fields['newsNumber'],
            'layout_type' => (int) $fields['layoutOrientation'],
            'tickerSpeed' => (int) $fields['tickerSpeed'],
            'tickerOrientation' => $fields['tickerOrientation'],
            'tickerHeight' => (int) $fields['tickerHeight'],
            'showTicker' => (int) $fields['showTicker'],
            'slideDelay' => (int) $fields['slideDelay'],
        ];

        try {
            $this->repository->save($newSettings);
        } catch (\RuntimeException $e) {
            $logger->error('Failed to save settings: '.$e->getMessage());

            return new JsonResponse(['status' => 'error', 'message' => 'Failed to save settings.'], 500);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
