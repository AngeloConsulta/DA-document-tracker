<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function documents()
    {
        return $this->hasMany(Document::class, 'status_id');
    }

    public function fromHistories()
    {
        return $this->hasMany(DocumentHistory::class, 'from_status_id');
    }

    public function toHistories()
    {
        return $this->hasMany(DocumentHistory::class, 'to_status_id');
    }
}
