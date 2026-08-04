<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait RequestValidationTrait
{
    private function requireFields(Request $request, array $fields): array
    {
        $values = [];
        foreach ($fields as $field) {
            $value = $request->request->get($field, '');
            if ('' === $value) {
                throw new BadRequestHttpException("Missing required field: {$field}");
            }
            $values[$field] = $value;
        }

        return $values;
    }
}
