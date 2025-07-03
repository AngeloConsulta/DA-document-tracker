<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document_sub_type_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subType()
    {
        return $this->belongsTo(DocumentSubType::class, 'document_sub_type_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_status_id');
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
