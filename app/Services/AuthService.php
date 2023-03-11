<?php
    namespace App\Services;

    use JWTAuth;
    use Exception;
    use App\Models\User;
    use App\Http\Requests\AuthRequest;

    class AuthService {

        public function returnCondition($condition, $errorCode, $message)
        {
            return response()->json([
                'success' => $condition,
                'message' => $message,
            ], $errorCode);
        }
        
        public function login(AuthRequest $request)
        {
            try {

                $check = User::where('email', $request->email)->first();
                if(!$check)
                    return $this->returnCondition(false, 404, 'email not found');

                if (!$token = auth()->attempt([
                    'email'     => $request->email,
                    'password'  => $request->password,
                ])) {
                    return $this->returnCondition(false, 401, 'incorrect password');
                }

                return response()->json([
                    'success' => true,
                    'data'    => [
                        'token'      => $token,
                        'expires_in' => auth()->factory()->getTTL() * 60,
                    ]
                ], 200);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function me()
        {
            try {

                return response()->json([
                    'success' => true,
                    'data'    => auth()->user(),
                ], 200);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function logout()
        {
            try {
                auth()->logout();

                JWTAuth::getToken(); // make sure token loaded, this method return token
                JWTAuth::invalidate(true); // blaclist forever = true

                return $this->returnCondition(true, 200, 'Successfully logged out');
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }
    }
?>