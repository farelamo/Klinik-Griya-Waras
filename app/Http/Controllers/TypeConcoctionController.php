<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TypeConcoctionService;
use App\Http\Requests\TypeConcoctionRequest;

class TypeConcoctionController extends Controller
{
    public function __construct(TypeConcoctionService $service)
    {
        $this->middleware(['pharmacist'])->except(['index']);
        $this->service = $service;
    }

    public function index()
    {
        if(
            auth()->user()->role == 'superadmin' ||
            auth()->user()->role == 'pharmacist' ||
            auth()->user()->role == 'doctor'
        )
            return $this->service->index();
        
        return response()->json([
            'success' => false,
            'message' => 'invalid role access',
        ], 401);
    }

    public function show($id)
    {
        return $this->service->show($id);
    }

    public function store(TypeConcoctionRequest $request)
    {
        return $this->service->store($request);
    }

    public function update($id, TypeConcoctionRequest $request)
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}