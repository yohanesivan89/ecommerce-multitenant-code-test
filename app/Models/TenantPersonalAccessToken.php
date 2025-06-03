<?php
namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class TenantPersonalAccessToken extends SanctumToken
{
    /**
     * Force this model to always use the "tenant" connection.
     */
    protected $connection = 'tenant';

    protected $table = 'personal_access_tokens';
}
