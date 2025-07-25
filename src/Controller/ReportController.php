<?php

namespace App\Controller;

use App\Dto\RequestDto\Report\ReportOrderGenerationRequestDto;
use App\Dto\ResponseDto\Report\ReportResponseDto;
use App\Exception\UnknownEnumTypeException;
use App\Service\Report\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    public function __construct(
        private readonly ReportService $reportService,
    )
    {
    }

    /**
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface
     */
    #[Route('/api/report/order-generation', name: 'report', methods: ['POST'])]
    public function orderReportGeneration(
        #[MapRequestPayload]
        ReportOrderGenerationRequestDto $requestDto
    ): JsonResponse
    {
        $report = $this->reportService->orderReportGeneration($requestDto);

        return new JsonResponse(
            data: new ReportResponseDto(
                id: $report->getId(),
                reportType: $report->getReportType(),
                status: $report->getStatus(),
                detail: $report->getDetail(),
                filePath: $report->getFilePath(),
                dateFrom: $report->getDateFrom(),
                dateTo: $report->getDateTo(),
                createdAt: $report->getCreatedAt(),
            ),
            status: Response::HTTP_CREATED,
        );
    }
}
