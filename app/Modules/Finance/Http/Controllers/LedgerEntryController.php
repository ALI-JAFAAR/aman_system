<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\LedgerEntry;
use App\Modules\Shared\Http\Controllers\CrudController;

class LedgerEntryController extends CrudController
{
    protected string $modelClass = LedgerEntry::class;
}

