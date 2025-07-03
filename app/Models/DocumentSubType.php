<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSubType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_type_id'];

    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function statuses()
    {
        return $this->hasMany(DocumentStatus::class, 'document_sub_type_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_sub_type_id');
    }
} 