<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    //
    public function index(){
        return response()->json(User::all());
    }

    public function edit(Request $request){
        $user = User::where('email', $request->json()->get('email'));
    }

    public function delete(){
        $user = User::where('email', $request->json()->get('email'))->delete();

        return response()->json(['status' => 'ok'], 200);
    }
}
