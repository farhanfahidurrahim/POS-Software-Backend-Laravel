<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\user\UserRequest;
use App\Http\Controllers\API\BaseController as BaseController;

class RegisterController extends BaseController
{
    use UploadAble;

    public function register(UserRequest $request)
    {

        try {
            $validatedData = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation Error.', $e->errors());
        }
        $valid = $validatedData;
        $valid['password'] = bcrypt($valid['password']);
        $user = new User();
        $user->fill($valid);
        if ($request->file('image')) {
            $filename = $this->uploadOne($request->image, 500, 500, config('imagepath.user'));
            $user->image = $filename;
        }
        $user->save();
        return $this->sendResponse($user, 'User registered successfully.');
    }


    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['user'] =  $user;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation Error.', $e->errors());
        }
        $user = User::find($id);



        if (!$user) {
            return $this->sendError('User not found.');
        }
        $user->update($validatedData);
        if ($request->hasFile('image')) {
            $filename = $this->uploadOne($request->image, 500, 500, config('imagepath.user'));
            $this->deleteOne(config('imagepath.user'), $user->image);
            $user->update(['image' => $filename]);
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }
        return $this->sendResponse($user, 'User updated successfully.');
    }
}
