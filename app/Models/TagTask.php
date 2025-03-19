<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TagTask extends Pivot
{
    /**
     * @var string|null
     */
    protected $table = 'tag_task';
}
