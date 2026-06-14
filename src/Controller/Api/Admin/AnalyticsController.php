<?php

declare(strict_types=1);

namespace App\Controller\Api\Admin;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AnalyticsController extends AbstractController
{
    public function __construct(
        private readonly string $googleAnalyticsPropertyId,
        private readonly string $googleAnalyticsCredentialsPath,
    ) {
    }

    #[Route('/stats/visits', name: 'api_admin_stats_visits', methods: ['GET'])]
    public function pageviews(): JsonResponse
    {
        try {
            error_log('GA: création du client');
            $client = new BetaAnalyticsDataClient([
                'credentials' => $this->googleAnalyticsCredentialsPath,
                'transport' => 'rest',
            ]);
            error_log('GA: client créé');

            $request = (new RunReportRequest())
                ->setProperty($this->googleAnalyticsPropertyId)
                ->setDateRanges([
                    new DateRange([
                        'start_date' => '30daysAgo',
                        'end_date' => 'today',
                    ]),
                ])
                ->setDimensions([
                    new Dimension(['name' => 'date']),
                ])
                ->setMetrics([
                    new Metric(['name' => 'screenPageViews']),
                    new Metric(['name' => 'activeUsers']),
                ])
                ->setOrderBys([
                    (new OrderBy())->setDimension(
                        (new DimensionOrderBy())->setDimensionName('date')
                    ),
                ]);

            error_log('GA: avant runReport');
            $response = $client->runReport($request);
            error_log('GA: après runReport');
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $rows = [];
        foreach ($response->getRows() as $row) {
            $rawDate = $row->getDimensionValues()[0]->getValue();

            $rows[] = [
                'date' => sprintf(
                    '%s-%s-%s',
                    substr($rawDate, 0, 4),
                    substr($rawDate, 4, 2),
                    substr($rawDate, 6, 2),
                ),
                'pageViews' => (int) $row->getMetricValues()[0]->getValue(),
                'activeUsers' => (int) $row->getMetricValues()[1]->getValue(),
            ];
        }

        return $this->json([
            'data' => $rows,
        ]);
    }
}
