<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            $role->save();
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
            $permission->save();
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
}
