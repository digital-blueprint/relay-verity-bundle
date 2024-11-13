<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\State;

use ApiPlatform\Metadata\Operation;
use Dbp\Relay\CoreBundle\Rest\AbstractDataProvider;
use Dbp\Relay\ValidationBundle\ApiResource\ValidationReport;

/**
 * @extends AbstractDataProvider<ValidationReport>
 */
class ValidationReportStateProvider extends AbstractDataProvider
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return new ValidationReport();
    }

    protected function getItemById(string $id, array $filters = [], array $options = []): ?object
    {
        return new ValidationReport('new-id-25');
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
