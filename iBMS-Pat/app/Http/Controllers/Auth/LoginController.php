<?php
/*
* <System Name> iBMS
* <Program Name> LoginController.php
*
* <Create> 2018.07.10 TP Bryan
* <Update> 2018.12.14 TP Robert
*
*/

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Traits\CommonFunctions;
use App\User;
/**
 * <Class Name> LoginController
 *
 * <Overview> This controller handles authenticating users for the application and
 *            redirecting them to your home screen. The controller uses a trait
 *            to conveniently provide its functionality to your applications.
 *            (included in Laravel's default authentication)
 */
class LoginController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // validateLogin                   (1.0) Get the needed authorization credentials from the request and validate if the user is active or not.
    //

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * Where to redirect users after login.
     * (Extended from AuthenticatesUsers trait)
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     * (Extended from AuthenticatesUsers trait)
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * The user has been authenticated.
     * (Extended from AuthenticatesUsers trait)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    // protected function authenticated(Request $request, $user)
    // {
    //     return redirect('/');
    // }


    /**
     * <Layer number> (1.0) Get the needed authorization credentials from the request and validate if the user is active or not.
     * <Processing name> validateLogin
     * <Function>
     *            URL: http://localhost/login/
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    protected function validateLogin(Request $request)
    {
        $user = User::where('USERNAME', $request->username)->first();
        $message = 'The account you are trying to login is not registered or it has been disabled.';
        //check if user is found
        if($user){
            //check if the user is disabled
            if($user['REG_FLAG'] == 0){
                $errorMsg = [$this->username() => trans('auth.disabled')];
                $message = trans('auth.disabled');
                $this->storeLogs(4, 'Manual', $errorMsg['username'], $request->ip(), $request->username);
            }else if($user['REG_FLAG'] == 1){
                //check password
                if(!\Hash::check($request->password, $user->PASSWORD)){
                    $errorMsg = [$this->username() => trans('auth.failed')];
                    $this->storeLogs(4, 'Manual', $errorMsg['username'], $request->ip(), $request->username);
                }
            }else{

            }
        }

        $this->validate($request, [
            $this->username() => 'required|exists:M001_USERS,' . $this->username() . ',REG_FLAG,1',
            'password' => 'required',
        ],[
            $this->username() . '.exists' => $message,
        ]);
    }
}
