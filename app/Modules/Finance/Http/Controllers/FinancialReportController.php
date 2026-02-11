<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\FinancialReport;
use App\Modules\Shared\Http\Controllers\CrudController;

class FinancialReportController extends CrudController
{
    protected string $modelClass = FinancialReport::class;
}

