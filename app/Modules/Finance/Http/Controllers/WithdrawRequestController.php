<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\WithdrawRequest;
use App\Modules\Shared\Http\Controllers\CrudController;

class WithdrawRequestController extends CrudController
{
    protected string $modelClass = WithdrawRequest::class;
}

