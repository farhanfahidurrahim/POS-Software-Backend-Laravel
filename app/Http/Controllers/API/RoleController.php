<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('can:role.show')->only('index');
//        $this->middleware('can:role.add')->only(['create', 'store']);
//        $this->middleware('can:role.edit')->only(['edit', 'update']);
//    }

    public function index()
    {
        $roles = Role::where('guard_name', 'web')->where('id', '!=', 2)->orderBy('id', 'desc')->get();
        if($roles->isEmpty()){
            return response()->json(['message' => 'No Roles found'], 200);
        }
        return RoleResource::collection($roles);

    }

    public function permission()
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        if($permissions->isEmpty()){
            return response()->json(['message' => 'No Permission found'], 200);
        }
        return PermissionResource::collection($permissions);
    }

    public function store(RolePermissionRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $permissions = $request->permissions;
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
            return response()->json(['message' => 'role created successfully'], 200);
        }
        return response()->json(['message' => 'shamim'], 200);
    }

//    public function show($id)
//    {
//        //
//    }

    public function edit($id)
    {
        try {
            $role = Role::findById($id);
            $permissions = Permission::where('guard_name', 'web')->get();
            return response()->json([
                'role' => $role,
                'permissions' => $permissions
            ]);
        } catch (DecryptException $e) {
                abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:24', 'min:2',
                    Rule::unique('roles')->ignore($id)->where('guard_name', 'web')
                ],
                'permissions' => 'required|array',
            ]);

            $role = Role::findById($id);
            $permissions = $request->input('permissions');
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
                $role->name = $request->name;
                $role->update();
                return response()->json(['message' => 'Role Update successfully'], 200);
            }
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}
