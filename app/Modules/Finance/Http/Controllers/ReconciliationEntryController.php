<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\ReconciliationEntry;
use App\Modules\Shared\Http\Controllers\CrudController;

class ReconciliationEntryController extends CrudController
{
    protected string $modelClass = ReconciliationEntry::class;
}

