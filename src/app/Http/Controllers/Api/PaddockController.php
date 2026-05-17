<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paddock;
use Illuminate\Http\Request;
class PaddockController extends Controller
{
    public function index()
    {
        $paddocks = Paddock::withCount(['users', 'adverts', 'posts', 'events'])->get();
        
        return response()->json($paddocks);
    }

    public function show($id)
    {
        $paddock = Paddock::with(['users', 'adverts', 'posts', 'events'])
            ->withCount(['users', 'adverts', 'posts', 'events'])
            ->findOrFail($id);
        
        return response()->json($paddock);
    }
}
