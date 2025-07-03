<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_number',
        'title',
        'description',
        'document_type_id',
        'status_id',
        'department_id',
        'source',
        'received_at',
        'sent_at',
        'created_by',
        'current_assignee',
        'file_path',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function status(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class, 'status_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_assignee');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function isIncoming()
    {
        return is_null($this->sent_at);
    }

    public function isOutgoing()
    {
        return !is_null($this->sent_at);
    }

    // Query Scopes
    public function scopeAccessibleTo($query, $user)
    {
        // User can access documents they created, assigned to them, or in their department
        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('current_assignee', $user->id)
              ->orWhere('department_id', $user->department_id);
        });
    }

    public function scopeIncoming($query, $user)
    {
        // Incoming: assigned to user or user's department, not created by user
        return $query->where(function ($q) use ($user) {
            $q->where('current_assignee', $user->id)
              ->orWhere('department_id', $user->department_id);
        })->where('created_by', '!=', $user->id);
    }

    public function scopeOutgoing($query, $user)
    {
        // Outgoing: all documents created by user or user's department
        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('department_id', $user->department_id);
        });
    }
}
