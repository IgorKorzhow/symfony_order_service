<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExistsEntityByField extends Constraint
{
    public string $message = 'Entity with value "{{ value }}" does not exist.';

    public string $entityClass;

    public string $field;

    // all configurable options must be passed to the constructor
    public function __construct(string $entityClass, string $field, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->entityClass = $entityClass;
        $this->field = $field;
    }
}
