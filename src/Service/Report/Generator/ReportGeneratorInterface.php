<?php

declare(strict_types=1);

namespace App\Service\Report\Generator;

use App\Entity\Report;

interface ReportGeneratorInterface
{
    public function generate(Report $report): Report;
}
