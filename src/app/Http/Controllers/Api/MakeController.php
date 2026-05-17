<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Make;
use Illuminate\Http\Request;
class MakeController extends Controller
{
    public function index()
    {
        $makes = Make::with(['models', 'engines'])->get();
        
        return response()->json($makes);
    }

    public function show($id)
    {
        $make = Make::with(['models', 'engines'])->findOrFail($id);
        
        return response()->json($make);
    }
}
