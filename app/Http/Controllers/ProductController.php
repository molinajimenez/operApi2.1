<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
class ProductController extends Controller
{
    //
    public function index(){
        return response()->json(['data' => Product::all()], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->json()->all(), [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        if($validator->fails()){
            return response()->json(['status' => failed], 401);
        }

        Product::create([
            'name' => $request->json()->get('name'),
            'category' => $request->json()->get('category'),
            'description' => $request->json()->get('description'),
        ])->save();

        return response()->json(['status' => 'ok'], 200);
    }
}
