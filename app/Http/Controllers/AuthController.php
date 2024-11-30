<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','refresh']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $refreshToken = $this->createRefreshToken();
        return $this->respondWithToken($token,$refreshToken);
    }

   

    public function profile()
    {
        try{
            return response()->json(auth('api')->user());
        }catch(JWTException $exception){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->refresh_token;
        try{
            $decode = JWTAuth::getJWTProvider()->decode($refreshToken);
            /** Xử lý cấp lại Token mới
             * => Lấy thông tin user
            */
            $user = User::find($decode['user_id']);
            if (!$user){
                return response()->json(['error' => 'User Not Found'], 404);
            }
            auth('api')->invalidate(); // Vô hiệu hoá Token hiệm tại

            $token = auth('api')->login($user); // Tạo mới Token

            $refreshToken = $this->createRefreshToken();
            return $this->respondWithToken($token,$refreshToken);

        }catch(JWTException $exception){
            return response()->json(['error' => 'Refresh Token Invalid'], 500);
        }
    }

    private function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    private function createRefreshToken(){
        $data = [
            'user_id' => auth('api')->user()->id,
            'radom' => rand().time(),
            'exp' => time() + config('jwt.refresh_ttl')
        ];
        $refreshToken = JWTAuth::getJWTProvider()->encode($data);
        return $refreshToken;
    }
}
