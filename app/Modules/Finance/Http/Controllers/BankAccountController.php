<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Models\BankAccount;
use App\Modules\Shared\Http\Controllers\CrudController;

class BankAccountController extends CrudController
{
    protected string $modelClass = BankAccount::class;
}

