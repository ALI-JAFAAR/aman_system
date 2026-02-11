<?php

namespace App\Modules\Wallet\Http\Controllers;

use App\Models\Wallet;
use App\Modules\Shared\Http\Controllers\CrudController;

/**
 * Legacy CRUD for Wallet model.
 */
class WalletCrudController extends CrudController
{
    protected string $modelClass = Wallet::class;
}

