<?php

declare(strict_types=1);

namespace App\Factory\Message;

use App\Entity\ReportDetail;
use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use App\Message\Report\ReportGeneratedMessage;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ReportGeneratedMessageFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function fromArray(array $data): ReportGeneratedMessage
    {
        $this->validateData($data);

        return new ReportGeneratedMessage(
            id: new Uuid($data['reportId']),
            result: ReportStatusEnum::typeByString($data['result']),
            detail: isset($data['detail']) && $data['detail']
                ? new ReportDetail(
                    message: $data['message'],
                    error: $data['error']
                )
                : null,
        );
    }

    private function validateData(array $data): void
    {
        $violations = $this->validator->validate($data, $this->validationConstraints());

        if (count($violations) > 0) {
            throw new \InvalidArgumentException((string) $violations);
        }
    }

    private function validationConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'reportId' => [
                new Assert\NotNull(),
                new Assert\Uuid(),
            ],
            'result' => [
                new Assert\NotNull(),
                new Assert\Choice(callback: [ReportTypeEnum::class, 'values']),
            ],
            'detail' => new Assert\Optional([
                new Assert\Collection([
                    'error' => [
                        new Assert\Required([
                            new Assert\NotNull(),
                            new Assert\Type('string'),
                        ]),
                    ],
                    'message' => [
                        new Assert\Required([
                            new Assert\NotNull(),
                            new Assert\Type('string'),
                        ]),
                    ],
                ]),
            ]),
        ]);
    }
}
