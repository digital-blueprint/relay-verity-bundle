<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\State;

use ApiPlatform\Metadata\Operation;
use Dbp\Relay\CoreBundle\Rest\AbstractDataProvider;
use Dbp\Relay\VerityBundle\ApiResource\VerityReport;

/**
 * @extends AbstractDataProvider<VerityReport>
 */
class VerityReportStateProvider extends AbstractDataProvider
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return new VerityReport();
    }

    protected function getItemById(string $id, array $filters = [], array $options = []): ?object
    {
        return new VerityReport('new-id-25');
    }

    protected function getPage(int $currentPageNumber, int $maxNumItemsPerPage, array $filters = [], array $options = []): array
    {
        return [];
    }

    protected function isUserGrantedOperationAccess(int $operation): bool
    {
        return true;
    }
}
