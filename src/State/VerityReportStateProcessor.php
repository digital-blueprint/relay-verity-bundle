<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\State;

use Dbp\Relay\CoreBundle\Rest\AbstractDataProcessor;
use Dbp\Relay\VerityBundle\ApiResource\VerityReport;
use Dbp\Relay\VerityBundle\Service\VerityService;

class VerityReportStateProcessor extends AbstractDataProcessor
{
    public function __construct(private readonly VerityService $validationService)
    {
        parent::__construct();
    }

    public function addItem(mixed $data, array $filters): mixed
    {
        assert($data instanceof VerityReport);

        return $this->validationService->validate($data->uuid,
            $data->file->getContent(),
            $data->file->getFilename(),
            $data->file->getSize(),
            $data->fileHash,
            $data->profile,
            $data->file->getMimeType());
    }
}
