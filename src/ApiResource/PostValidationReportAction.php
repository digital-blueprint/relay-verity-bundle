<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\ApiResource;

use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\CoreBundle\Rest\CustomControllerTrait;
use Dbp\Relay\VerityBundle\Service\VerityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostValidationReportAction extends AbstractController
{
    use CustomControllerTrait;

    public function __construct(
        private readonly VerityService $validationService)
    {
    }

    /**
     * @throws HttpException
     * @throws \JsonException
     * @throws \Exception
     */
    public function __invoke(Request $request): VerityReport
    {
        if ($request->files->get('file') === null) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'No file with parameter key "file" was received!',
                'verity:create-file-data-missing-file');
        }

        $uuid = $request->request->get('uuid');

        //        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $profileName = $request->request->get('profile');
        if ($profileName === '' || $profileName === null) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'Missing "profile" attribute! '.print_r($request->query->all(), true),
                'verity:create-report-missing-profile');
        }

        //        $this->checkProfilePermissions($profileName);

        /** @var File $uploadedFile */
        $uploadedFile = $request->files->get('file');

        // Check if there is an uploaded file
        if (!$uploadedFile) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'Missing or empty "file" attribute!',
                'verity:create-report-missing-file');
        }

        $fileContent = $uploadedFile->getContent();
        $mimetype = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();
        $fileName = $uploadedFile->getFilename();
        $fileHash = $request->request->get('fileHash');
        // Calculate the sha1 checksum
        $sha1checksum = sha1($fileContent);
        if ($fileHash !== '' && $fileHash !== $sha1checksum) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'File hash mismatch!',
                'verity:create-report-file-hash-mismatch');
        }
        $fileHash = $sha1checksum;

        $report = $this->validationService->validate($uuid, $uploadedFile, $fileName, $fileSize, $fileHash, $profileName, $mimetype);

        return $report;
    }
}
