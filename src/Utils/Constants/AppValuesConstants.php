<?php

namespace App\Utils\Constants;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppValuesConstants
{
    public static function validation (Request $request, ValidatorInterface $validator): bool| array
    {
        $errors = $validator->validate($request);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $message = $error->getMessage();
                $errorMessages[$field][] = $message;
            }
            return  $errorMessages;
        }
        return true;
    }
}
