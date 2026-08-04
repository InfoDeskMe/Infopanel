<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait CacheableJsonResponseTrait
{
    /**
     * The panel polls these endpoints continuously; an ETag lets unchanged
     * data round-trip as a 304 instead of the full payload every time.
     */
    private function jsonWithCaching(Request $request, array $payload): Response
    {
        $response = new JsonResponse($payload);
        $response->setEtag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);

        return $response;
    }
}
