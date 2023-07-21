<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendWelcomeEmail;
use App\Mail\WelcomeEmail;

class UserController extends Controller
{
    public function index()
    {
        $perPage = 50; // Número de usuarios por página
        $users = User::paginate($perPage);
    
        $response = [
            'status' => 'success',
            'message' => 'Users found!',
            'data' => [
                'users' => $users->items(),
                'currentPage' => $users->currentPage(),
                'perPage' => $users->perPage(),
                'totalPages' => $users->lastPage(),
                'totalCount' => $users->total(),
            ],
        ];

        return response()->json($response, Response::HTTP_OK);
    }
    
    public function show($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found.', 'type' => 'error'], 404);
        }
    
        return response()->success($user, 'User found!');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'address' => 'nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->address = 'default';
        $user->save();

        $token = JWTAuth::fromUser($user);
        dispatch(new SendWelcomeEmail(
            $user->name,
            $user->email
        )); 

        return response()->success(['token' => $token,'user' => $user ], 'User successfully registered!');
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
                'password' => 'required|min:6',
                'address' => 'nullable|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
    
            $user->name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
    
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
    
            $user->save();
    
            return response()->success($user, 'User has been successfully updated');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
        }
    }                   
    
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => "Deleted"], Response::HTTP_OK);
    }
}
