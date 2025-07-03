<?php

namespace App\Http\Controllers;

use App\Models\DocumentSubType;
use Illuminate\Http\Request;

class DocumentSubTypeController extends Controller
{
    public function statuses($subTypeId)
    {
        $subType = DocumentSubType::with('statuses')->findOrFail($subTypeId);
        return response()->json($subType->statuses);
    }
} 