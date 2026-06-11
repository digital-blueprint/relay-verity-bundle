<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Authorization;

use Dbp\Relay\CoreBundle\Authorization\AbstractAuthorizationService;
use Dbp\Relay\VerityBundle\DependencyInjection\Configuration;

class AuthorizationService extends AbstractAuthorizationService
{
    /**
     * Check if the user can access the application at all.
     */
    public function checkCanUse(): void
    {
        $this->denyAccessUnlessIsGrantedRole(Configuration::ROLE_USER);
    }

    /**
     * Returns if the user can use the application at all.
     */
    public function getCanUse(): bool
    {
        return $this->isGrantedRole(Configuration::ROLE_USER);
    }
}
