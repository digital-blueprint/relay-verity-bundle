<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use Dbp\Relay\VerityBundle\State\VerityReportStateProvider;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    uriTemplate: '/verity/reports',
    shortName: 'Validation Report',
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new Post(
            inputFormats: [
                'multipart' => 'multipart/form-data',
            ],
            outputFormats: [
                'jsonld' => 'application/ld+json',
            ],
            controller: PostValidationReportAction::class,
            openapiContext: [
                'parameters' => [],
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['file', 'uuid', 'profile'],
                                'properties' => [
                                    'uuid' => [
                                        'description' => 'The id (as UUID) of the report',
                                        'type' => 'string',
                                        'example' => 'cbe2a804-3948-4de9-b000-5cd65f657b2f',
                                    ],
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                    'fileHash' => [
                                        'description' => 'Sha256 hash of the file. If one is provided, then it has to match the actual sha256 hash of the uploaded file!',
                                        'type' => 'string',
                                        'example' => '',
                                    ],
                                    'profile' => [
                                        'description' => 'The profile name to validate the file for',
                                        'type' => 'string',
                                        'example' => 'archive',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            security: 'is_granted("IS_AUTHENTICATED_FULLY")',
            deserialize: false,
        ),
    ],
    outputFormats: ['jsonld' => ['application/ld+jason']],
    normalizationContext: ['groups' => ['report:read']],
    denormalizationContext: ['groups' => ['report:write']],
    openapiContext: [
    ],
    provider: VerityReportStateProvider::class,
)]
class VerityReport
{
    #[ApiProperty(identifier: true)]
    public ?string $uuid = null;

    #[ApiProperty]
    public ?File $file = null;
    #[ApiProperty]
    public ?string $fileHash = null;
    #[ApiProperty]
    public ?string $profile = null;
    #[ApiProperty]
    public bool $valid = false;
    #[ApiProperty]
    public string $message = '';
    #[ApiProperty]
    public array $errors = [];

    public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid;
        $this->valid = false;
        $this->message = 'not validated';
    }

    #[Groups(['report:read'])]
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    #[Groups(['report:write'])]
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    #[Groups(['report:write'])]
    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    #[Groups(['report:read'])]
    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    #[Groups(['report:write'])]
    public function setFileHash(?string $fileHash): void
    {
        $this->fileHash = $fileHash;
    }

    #[Groups(['report:read'])]
    public function getProfile(): ?string
    {
        return $this->profile;
    }

    #[Groups(['report:write'])]
    public function setProfile(string $profile): void
    {
        $this->profile = $profile;
    }

    #[Groups(['report:read'])]
    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    #[Groups(['report:read'])]
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    #[Groups(['report:read'])]
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
