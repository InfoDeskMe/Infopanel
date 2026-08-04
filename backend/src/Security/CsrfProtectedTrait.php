<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * For controllers extending AbstractController, which provides isCsrfTokenValid().
 */
trait CsrfProtectedTrait
{
    private function assertValidCsrfToken(Request $request): void
    {
        if (!$this->isCsrfTokenValid('api', $request->headers->get('X-CSRF-Token'))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }
}
