<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $entity): array
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) === 0) {
            return [];
        }

        return array_map(fn($error) => $error->getMessage(), iterator_to_array($errors));
    }
}
