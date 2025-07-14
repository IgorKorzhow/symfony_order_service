<?php

namespace App\Dto;

use App\Exception\DtoValidationException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidationDto
{
    /**
     * @throws DtoValidationException
     */
    public function validate(
        ValidatorInterface $validator,
    ): void {
        $errors = $validator->validate($this);

        $errors->addAll($this->extendValidation());

        if (count($errors) > 0) {
            throw new DtoValidationException($this->formatErrors($errors));
        }
    }

    protected function extendValidation(): ConstraintViolationListInterface
    {
        return new ConstraintViolationList();
    }

    protected function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];

        foreach ($errors as $violation) {
            $property = $violation->getPropertyPath();
            $message = $violation->getMessage();

            $errorMessages[$property][] = $message;
        }

        return $errorMessages;
    }
}
