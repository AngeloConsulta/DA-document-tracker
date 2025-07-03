<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_approval',
        'is_active'
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function documents()
    {
        // Get all documents for this type via its sub-types
        return Document::whereIn('document_sub_type_id', $this->subTypes()->pluck('id'));
    }

    public function subTypes()
    {
        return $this->hasMany(DocumentSubType::class);
    }
}
