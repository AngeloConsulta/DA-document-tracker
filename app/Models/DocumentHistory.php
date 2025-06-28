<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'from_status_id',
        'to_status_id',
        'from_department_id',
        'to_department_id',
        'from_user_id',
        'to_user_id',
        'remarks',
        'action_type'
    ];

    // Relationships
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromStatus()
    {
        return $this->belongsTo(DocumentStatus::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(DocumentStatus::class, 'to_status_id');
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
