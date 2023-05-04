<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Register function
    public function register(Request $request){

        $data = $request->validate([
            'name' => 'required|string',
            'lastname' => 'string',
            'username' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = $user->createToken('TokenAPI')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // Login function
    public function login(Request $request){

        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $response = ["status" => 0, "msg" => ""];

        $user = User::where('email', $data['email'])->firstOrFail();

        if($user){

            if(Hash::check($data['password'], $user->password)){

                $token = $user->createToken('TokenAPI')->plainTextToken;

                $response["msg"] = "Token: " . $token;
                $response["status"] = 200;
            }
            else{
                $response["msg"] = "Credenciales incorrectas.";
                $response["status"] = 401;
            }
        }
        else{
            $response["msg"] = "Usuario no encontrado.";
            $response["status"] = 401;
        }

        return response($response, 200);
    }
}