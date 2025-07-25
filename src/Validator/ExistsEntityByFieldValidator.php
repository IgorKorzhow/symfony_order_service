<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistsEntityByFieldValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistsEntityByField) {
            throw new UnexpectedTypeException($constraint, ExistsEntityByField::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if ($value === null || $value === '') {
            return;
        }

        $repository = $this->registry->getManager()->getRepository($constraint->entityClass);

        $result = $repository->findOneBy([$constraint->field => $value]);

        if (isset($result)) {
            return;
        }

        // the argument must be a string or an object implementing __toString()
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $constraint->field)
            ->addViolation();
    }
}
