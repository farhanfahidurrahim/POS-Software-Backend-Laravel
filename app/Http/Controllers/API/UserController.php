<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    use UploadAble;
    public function allUser()
    {
        try {
            $users = User::all();
            foreach ($users as $user) {
                foreach ($user->getRoleNames() as $role) {
                    $roles = $role;
                }
            }
            return response()->json(['users' => $users]);
        } catch (DecryptException $e) {
            abort(404);
        }
    }
    public function get_role()
    {
        try {
            $roles = Role::where(['guard_name' => 'web'])->where('id', '!=', 1)->get();
            if ($roles->isEmpty()) {
                return response()->json(['message' => 'No roles found'], 200);
            }
            return response()->json(['data' => $roles], 200);
        } catch (DecryptException $e) {
            abort(404);
        }
    }


    public function storeAdminUser(AdminUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => trim($request->input('name')),
                'phone_number' => trim($request->input('phone_number')),
                'email' => trim(strtolower($request->input('email'))),
                'password' => Hash::make($request->input('password')),
            ]);
            if ($request->roles) {
                $user->assignRole($request->roles);
            }
            return response()->json(['message' => 'User created successfully', 'data' => $user], 200);
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function editAdminUser(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $roles = Role::where('guard_name', 'web')->where('id', '!=', 1)->get();
                return response()->json(['user' => $user, 'roles' => $roles]);
            } else {
                return response()->json(['message' => 'No User found'], 200);
            }
        } catch (DecryptException $e) {
            abort(404);
        }
    }
    public function updateAdminUser(Request $request, $id)
    {
        try {
            $request->validate([
                'roles' => 'required',
                'name' => 'required|string|max:64',
                'phone_number' => "required|bail|numeric|digits:11|regex:/^(?:\+?88)?01[3-9]\d{8}$/|unique:users,phone_number,$id",
                'email' =>  "required|string|max:32|unique:users,email,$id",
            ]);

            $user = User::find($id);

            if ($user) {
                $user->update([
                    'name' => trim($request->input('name')),
                    'phone_number' => trim($request->input('phone_number')),
                    'email' => trim(strtolower($request->input('email'))),
                ]);
                if ($request->roles) {
                    $user->syncRoles($request->roles);
                }
            } else {
                return response()->json(['message' => 'No User found'], 200);
            }
            return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function profileUpdate(Request $request)
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

    public function profilePasswordChange(Request $request)
    {
        $id = Auth::user()->id;
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);
        $profile = User::find($id);

        $current_password = $request->current_password;

        if (!Hash::check($current_password, $profile->password)) {
            return response()->json(['message' => 'Your Password is incorrect'], 422);
        }

        $profile->password = Hash::make($request->password);
        $profile->save();

        return response()->json(['message' => 'Your Password successfully changed!'], 200);
    }
}
