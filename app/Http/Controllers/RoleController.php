<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
class RoleController extends Controller
{
    //
    public function index(){
        $roles = DB::table('roles')->get();

        return response()->json($roles, 200);
    }

    public function store(Request $request){
        try{
            Role::create([
                'name' => $request->json()->get('role')
            ]);
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function edit(Request $request){
        $role = DB::table('roles')->where('name', $request->json()->get('role'))->get();
        try{
            $role->name = $request->json()->get('value');
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function delete(Request $request){
        
        try{
            $role = DB::table('roles')->where('name', $request->json()->get('role'))->delete();
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function indexPermission(){
        $permissions = DB::table('permissions')->get();

        return response()->json($permissions, 200);
    }

    public function storePermission(Request $request){
        try{
            Permission::create([
                'name' => $request->json()->get('permission')
            ]);
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function editPermission(Request $request){
        $permission = DB::table('permissions')->where('name', $request->json()->get('permission'))->get();
        try{
            $permission->name = $request->json()->get('value');
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function deletePermission(Request $request){
        
        try{
            $permission = DB::table('roles')->where('name', $request->json()->get('permission'))->delete();
            return response()->json(['status' => 'ok'], 200);
        } catch(Exception $exp){
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function AllRolesPermissions(Request $request){
        $allPermissions = DB::table('permissions')->get();
        $lookedIds = [];
        $roles = DB::table('roles')->get();
        $roles_response = [];
        
        foreach($roles as $role){
            $responseArray = [];
            $lookedIds = [];
            $idRole = $role->id;
            $existingPermissions = DB::table('role_has_permissions')->where('role_id', $idRole)->get();
            foreach ($allPermissions as $elem) {
                if (!in_array($elem->id, $lookedIds)) {
                    $perm = DB::table('permissions')->where('id', $elem->id)->first();
                    array_push($lookedIds, $elem->id);
                    array_push($responseArray, ['permission_id' => $elem->id, "name" => $perm->name, 'value' => false]);
                }
            }
    
            foreach ($existingPermissions as $existingElem) {
                foreach ($responseArray as $responseElem => $value) {
                        if ($existingElem->permission_id === $value['permission_id']) {
                        $responseArray[$responseElem]['value'] = true;
                    }
                }
            }

            $obj = [
                'id' => $role->id,
                'role' => $role->name,  //por ahora, despues pasamos nombre bonito.
                'permissions' => $responseArray
    
            ];

            array_push($roles_response, $obj);
        }
        
        return response()->json($roles_response, 200);
    }

    public function changeRolePermissions(Request $request){
        //delete and create depending if value field is true or false

        if ($request->json()->get('value')) {
            DB::table('role_has_permissions')->insert([
                'role_id' => $request->json()->get('roleId'),
                'permission_id' => $request->json()->get('permissionId'),
            ]);
        }else {
            try {
                DB::table('role_has_permissions')->where('role_id', $request->json()->get('roleId'))
                    ->where('permission_id', $request->json()->get('permissionId'))->delete();
            } catch (Exception $e) {
                return response()->json(['status' => 'could not update role permission', 'err' => $e->getMessage()], 400);
            }

        }

        return response()->json(['status' => 'updated role permission'], 200);
    }
}
