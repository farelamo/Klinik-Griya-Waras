<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicalRecordService;
use App\Http\Requests\MedicalRecordRequest;

class MedicalRecordController extends Controller
{
    public function __construct(MedicalRecordService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->index();
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
}
