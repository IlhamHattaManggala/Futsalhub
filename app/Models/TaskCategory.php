<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name'])]
class TaskCategory extends Model
{
    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }
}
