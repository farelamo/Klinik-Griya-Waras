<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->middleware(['double.admin'])->except(['index']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if(auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
            return $this->service->index($request);
            
        return response()->json([
            'success' => false,
            'message' => 'invalid role access',
        ], 401);
    }

    public function show($id)
    {
        return $this->service->show($id);
    }

    public function store(UserRequest $request)
    {
        return $this->service->store($request);
    }

    public function update($id, UserRequest $request)
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}