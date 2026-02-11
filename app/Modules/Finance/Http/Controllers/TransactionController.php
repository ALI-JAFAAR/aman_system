<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\Transaction;
use App\Modules\Shared\Http\Controllers\CrudController;

class TransactionController extends CrudController
{
    protected string $modelClass = Transaction::class;
}

