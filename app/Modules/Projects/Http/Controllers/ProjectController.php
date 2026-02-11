<?php

namespace App\Modules\Projects\Http\Controllers;

use App\Models\Project;
use App\Modules\Shared\Http\Controllers\CrudController;

class ProjectController extends CrudController
{
    protected string $modelClass = Project::class;
}

