<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['title', 'description', 'priority', 'due_date', 'status', 'assigned_to','assigned_by','rating'];

    //for table
    protected $table = 'users_tasks';
    // scoup by priority

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // scoup by status

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    // scope by user id

    public function scopeByUser($query, $id)
    {
        return $query->where('assigned_to', $id);
    }

    //primery keywords

    protected $primaryKey = 'task_id';
    public $incrementing = true;


    public function getDueDateAttribute($value)
    {
        $this->attributes['due_date']= Carbon::parse($value)->format('Y-m-d');
        return $this->attributes['due_date'];
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = Carbon::createFromFormat('Y-m-d', $value);
    }


    // for timestabe
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';




};
