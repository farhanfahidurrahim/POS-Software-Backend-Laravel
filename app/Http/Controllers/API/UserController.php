<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = Auth::user()->id;
        $request->validate([
            'name' => 'required|string|max:64',
            'phone_number' => "nullable|bail|numeric|digits:11|regex:/^(?:\+?88)?01[3-9]\d{8}$/|unique:users,phone_number,$id",
            // 'email' =>  "nullable|string|max:32|unique:users,email,$id",
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
            'password' => 'required',
        ]);

        $profile = User::find($id);

        if (!Hash::check($request->password, $profile->password)) {
            return response()->json(['message' => 'Your Password is incorrect'], 422);
        }

        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->phone_number = $request->phone_number;

        // Delete old image if it exists
        if($request->hasFile('image') && $profile->image){
            $oldImagePath = public_path(config('imagepath.user') . $profile->image);
            if(File::exists($oldImagePath)){
                File::delete($oldImagePath);
            }
        }
        // Updated image upload
        if ($request->hasFile('image')) {
            $filename = $this->uploadOne($request->image, 500, 500, config('imagepath.user'));
            $profile->image = $filename;
        }

        $profile->update();

        return response()->json(['message' => 'User updated successfully', 'data' => new UserResource($profile),], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
