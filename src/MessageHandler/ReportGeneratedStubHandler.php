<?php

namespace App\MessageHandler;

use App\Message\Report\ReportGeneratedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReportGeneratedStubHandler
{
    public function __invoke(ReportGeneratedMessage $message): void
    {
    }
}
