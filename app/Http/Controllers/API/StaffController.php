<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(){
        $data['staffs'] = User::orderBy('id','desc')->get();
        
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Product found'], 200);
        }
    }
}
