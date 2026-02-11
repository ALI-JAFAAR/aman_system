<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\Employee;
use App\Modules\Shared\Http\Controllers\CrudController;

class EmployeeController extends CrudController
{
    protected string $modelClass = Employee::class;
}

