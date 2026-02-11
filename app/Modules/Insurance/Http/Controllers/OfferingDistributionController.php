<?php

namespace App\Modules\Insurance\Http\Controllers;

use App\Models\OfferingDistribution;
use App\Modules\Shared\Http\Controllers\CrudController;

class OfferingDistributionController extends CrudController
{
    protected string $modelClass = OfferingDistribution::class;
}

