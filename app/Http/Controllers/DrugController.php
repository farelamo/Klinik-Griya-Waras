<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DrugService;
use App\Http\Requests\DrugRequest;

class DrugController extends Controller
{
    public function __construct(DrugService $service)
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

    public function store(DrugRequest $request)
    {
        return $this->service->store($request);
    }

    public function update($id, DrugRequest $request)
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}
