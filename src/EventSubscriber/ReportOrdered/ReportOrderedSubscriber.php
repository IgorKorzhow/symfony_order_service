<?php

namespace App\EventSubscriber\ReportOrdered;

use App\Message\Report\ReportOrderedMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class ReportOrderedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ReportOrderedMessage::class => [
            ['onReportOrdered', 0],
        ],
        ];
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function onReportOrdered(ReportOrderedMessage $event): void
    {
        $report = $event->getReport();


    }
}
