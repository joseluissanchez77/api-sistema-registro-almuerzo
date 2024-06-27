<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Repositories\AuthRepository;
use App\Traits\RestResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    use RestResponse;
    private $authRepository;

    /**
     * __construct
     *
     * @param App\Repositories\AuthRepository $authRepository
     * @return void
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }


    /**
     * login
     *
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request)
    {

        if(!Auth::attempt($request->toArray()))
            throw new AuthenticationException(__('messages.no-credentials'));
        // if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
        if(Auth::attempt($request->toArray()) ){ 
            $user = Auth::user(); 
            
            $token = $user->createToken('access_token');
            // $cookie = cookie('cookie_token', $token, 60*24);
            return $this->information(['Bearer'=>$token,"dataUser"=>$user])/* ->withoutCookie($cookie) */;
            // $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            // $success['name'] =  $user->name;
   
            // return $this->sendResponse($success, 'User login successfully.');
        }
       
        
        // else{ 
        //     // return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        // } 
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request)
    {
        // $cookie = Cookie::forget('cookie_token');
        auth()->user()->tokens()->delete();
        // $request->user()->token()->revoke();
        return $this->information(__('messages.logout'))/* ->withCookie($cookie) */;

    }

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
    public function register(UserRegisterRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->password = bcrypt($request->password);
            $user = new User($request->all());
            $this->authRepository->save($user);

            DB::commit();
            return $this->information(__('messages.register-user'));

        } catch (\Exception $ex) {
            DB::rollBack();
            throw new ConflictException($ex->getMessage());
        }
        
    }

    public function profile()
    {

    }
}
