<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Models\ClaimResponse;
use App\Modules\Shared\Http\Controllers\CrudController;

/**
 * Legacy CRUD for ClaimResponse (separate from nested /claims/{claim}/responses endpoints).
 */
class ClaimResponseCrudController extends CrudController
{
    protected string $modelClass = ClaimResponse::class;
}

