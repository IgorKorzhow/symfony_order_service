<?php

namespace App\Controller;

use App\Dto\Report\ReportDto;
use App\Exception\DtoValidationException;
use App\Exception\UnknownEnumTypeException;
use App\Service\Report\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReportController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly ReportService $reportService,
    )
    {
    }

    /**
     * @throws DtoValidationException
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface|\Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/api/report', name: 'report', methods: ['POST'])]
    public function store(ReportDto $reportDto)
    {
        $reportDto->validate($this->validator);

        $report = $this->reportService->orderReportGeneration($reportDto);

        return $this->json($this->serializer->normalize($report, 'json'), Response::HTTP_CREATED);
    }
}
