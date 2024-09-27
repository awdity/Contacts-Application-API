<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthController extends Controller
{
    // Register new user
    public function register(Request  $request)
    {
        //Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // If validation fails, throw a ValidationException
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->errors()],422);
        }

        try{
            // Create activation token
            $activation_token = Str::random(60);

            //creating a new user
            $user = User::create([
                'name'             => $request->name,
                'email'            => $request->email,
                'password'         => Hash::make($request->password),
                'activation_token' => $activation_token,
            ]);
    
            return response()->json(['activation_token' => $activation_token], 201);

        } catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred while registering the user. Please try again later.'], 500);
        }
    }

    // Activate user
    public function activateUser(Request $request)
    {
        // Validate the incoming request data
         $validator = Validator::make($request->all(), [
           'token' => 'required',
        ]);

        // If validation fails, throw a ValidationException
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->errors()],422);
        }

        try{
            $user = User::where('activation_token', $request->token)->first();
        
            if (!$user) {
                return response()->json(['message' => 'Invalid token'], 400);
            }
    
            $user->is_active = true;
            $user->activation_token = null;
            $user->save();
    
            return response()->json(['message' => 'User activated successfully']);

        } catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred while activating  the user. Please try again later.'], 500);
        }
    }

    // Login and generate JWT token
    public function login(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ]);

        // If validation fails, throw a ValidationException
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->errors()],422);
        }
        try{
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return response()->json(compact('token'));

        } catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }
}
