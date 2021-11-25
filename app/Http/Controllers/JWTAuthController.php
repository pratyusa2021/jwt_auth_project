<?php
namespace App\Http\Controllers;
use JWTAuth;
use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterAuthRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
class JwtAuthController extends Controller
{
	public $token = true;
	
	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), 
		[ 
		'name' => 'required',
		'email' => 'required|email',
		'password' => 'required',  
		'c_password' => 'required|same:password', 
		]);  
		if ($validator->fails()) {  
			return response()->json(['error'=>$validator->errors()], 401); 
		}   
		$user = new User();
		$user->name = $request->name;
		$user->email = $request->email;
		$user->password = bcrypt($request->password);
		$user->save();

		return response()->json([
		'success' => true,
		'data' => $user
		], Response::HTTP_OK);
	}
	public function login(Request $request)
	{
		$input = $request->only('email', 'password');
		$jwt_token = null;
		if (!$jwt_token = JWTAuth::attempt($input)) {
			return response()->json([
			'success' => false,
			'message' => 'Invalid Email or Password',
			], Response::HTTP_UNAUTHORIZED);
		}
		return response()->json([
		'success' => true,
		'token' => $jwt_token,
		]);
	}
}