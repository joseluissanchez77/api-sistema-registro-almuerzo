<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Repositories\AuthRepository;
use App\Traits\RestResponse;
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
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request)
    {
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
