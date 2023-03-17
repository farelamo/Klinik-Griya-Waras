<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicalRecordService;
use App\Http\Requests\MedicalRecordRequest;

class MedicalRecordController extends Controller
{
    public function __construct(MedicalRecordService $service)
    {
        $this->middleware('doctor')->except(['index', 'receipt', 'approvePharmacist']);
        $this->middleware('pharmacist')->only(['approvePharmacist']);
        $this->service = $service;
    }

    public function index()
    {
        if(auth()->user()->role == 'superadmin' || auth()->user()->role == 'doctor')
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

    public function store(MedicalRecordRequest $request)
    {
        return $this->service->store($request);
    }

    public function update($id, MedicalRecordRequest $request)
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }

    public function receipt()
    {
        if(auth()->user()->role == 'superadmin' || auth()->user()->role == 'pharmacist')
            return $this->service->receipt();
            
        return response()->json([
            'success' => false,
            'message' => 'invalid role access',
        ], 401);
    }
    
    public function approvePharmacist($id)
    {
        return $this->service->approvePharmacist($id);
    }
}