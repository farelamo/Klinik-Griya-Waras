<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Http\Requests\PatientRequest;

class PatientController extends Controller
{
    public function __construct(PatientService $service)
    {
        $this->middleware('admin')->except(['index']);
        $this->service = $service;
    }

    public function index()
    {
        if(
            auth()->user()->role == 'superadmin' ||
            auth()->user()->role == 'admin'      ||
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

    public function store(PatientRequest $request)
    {
        return $this->service->store($request);
    }

    public function update($id, PatientRequest $request)
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}