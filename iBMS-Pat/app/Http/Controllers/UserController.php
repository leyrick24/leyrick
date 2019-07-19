<?php
/*
* <System Name> iBMS
* <Program Name> UserController.php
*                ユーザーコントローラー画面
* <Create> 2018.08.31 TP Harvey
* <Update> 2018.11.21 TP Raymond Added code for modules
*          
*
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\AuthLocation;
use App\AuthModule;
use App\Traits\CommonFunctions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * <Class Name> UserController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class UserController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getUsers                  (1.0) Get all registered users.
    // getActiveUsers            (2.0) Get all active users.
    // getInactiveUsers          (3.0) Get all inactive users.
    // getUser                   (4.0) Get the specific user.
    // newUser                   (5.0) Create an account for the new user.
    // disableUser               (6.0) Disable the account of the user.
    // enableUser                (7.0) Enable the account of the user.
    // changePasswordUser        (8.0) Change the password of the user.
    // updateUserProfile         (9.0) Update the profile of the user.
    // updateUserDesignation     (10.0) Update the designation of the user.

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Get all registered users.
     * <Processing name> getUsers
     * <Function> Get all users that is registered
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return $user
     */
    public function getUsers(Request $request){
        // Check if what type of user
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = $request->route()->uri();
        if($request->USER_TYPE == 1){
            try{
                
                $user = User::with('authUserFloor')->with('authModules')->get();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            }
        }else if($request->USER_TYPE == 2){
            try{
                $user = User::where('USER_TYPE',3)->with('authUserFloor')
                ->with('authModules')->get(); 
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            }  
        }
        // Return all registered users
    	return $user;
    }
    /**
     * <Layer number> (2.0) Get all active users.
     * <Processing name> getActiveUsers
     * <Function> Get all users that is active
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return $user
     */
    public function getActiveUsers(Request $request){
        // Check if what type of user
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = $request->route()->uri();

        if($request->USER_TYPE == 1){
            try{
            $user = User::where('REG_FLAG',1)->with('authUserFloor')
                        ->with('authModules')->get();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            }
        }else if($request->USER_TYPE == 2){
            try{
            $user = User::where('REG_FLAG',2)->where('USER_TYPE',3)
                        ->with('authUserFloor')->with('authModules')->get();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            } 

        }
        // Return all active users
        return $user;
    }
    /**
     * <Layer number> (3.0) Get all inactive users.
     * <Processing name> getInactiveUsers
     * <Function> Get all users that is inactive
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return $user
     */
    public function getInactiveUsers(Request $request){
        // Check if what type of user
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = $request->route()->uri();
        if($request->USER_TYPE == 1){
            try{
            $user = User::where('REG_FLAG',0)->with('authUserFloor')
                        ->with('authModules')->get();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            }
        }else if($request->USER_TYPE == 2){
            try{
            $user = User::where('REG_FLAG',0)->where('USER_TYPE',3)
                        ->with('authUserFloor')->with('authModules')->get();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';   
            }
        }
        // Return all inactive users
        return $user;
    }
    /**
     * <Layer number> (4.0) Get the specific user.
     * <Processing name> getUser
     * <Function> Get the specific user
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return $user
     */
    public function getUser(Request $request){ 
            $ip = $request->ip();
            $username = auth()->user();
            $host = $username->USERNAME;
            $module = $request->route()->uri(); 
            $user_status = $request->USER_STATUS;
            $userid = $request->USER_ID;
            // Check if what type of user
        if($request->USER_TYPE == 1){
            try{
                $user = User::with('authUserFloor')->with('authModules')
                            ->where([['REG_FLAG',$user_status],
                                ['USER_ID',$userid]])->first();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';
            }
            }else if($request->USER_TYPE == 2){
            try{
                $user = User::where('REG_FLAG',$user_status)
                            ->where([['USER_TYPE',3],['USER_ID',$userid]])
                            ->select('USER_ID','USERNAME','USER_TYPE','EMAIL')
                            ->first();
            }catch(\Throwable $e){
                $this->auditLogs($ip,$host,$module,$e->getMessage());
                return 'failed';    
            }
            // Return the specific user
        	return $user;
        }

    }
    /**
     * <Layer number> (5.0) Create an account for the new user.
     * <Processing name> newUser
     * <Function> Create account for the new user
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return 'saved'
     */
    public function newUser(Request $request){
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'User Management';
        $instruction = 'Created a New User';
        $this->auditLogs($ip,$host,$module,$instruction);
        // Handle File Upload
        $userLogoPath = "storage/app/public/users";
        $file = $request->file;
        $fileName = $request->fileName;
        $filePath = '/'.$userLogoPath.'/'.$fileName;
        // Password of the new user
        $password = Hash::make($request->PASSWORD);
        // Details for creating new user
        $user = new User;
        $user->USER_TYPE = (int)$request->USER_TYPE;
        $user->USERNAME = $request->USERNAME;
        $user->PASSWORD = $password;
        $user->EMAIL = $request->EMAIL;
        $user->REG_FLAG = 1;
        if($request->hasFile == 1){
            $file->move(public_path($userLogoPath),$fileName);
            $user->USER_LOGO = $filePath;
        }
        // Upload Floor Image to specified location
        $user->save();
        $userid = $user->USER_ID;
        $floorids = explode(',', $request->floorids);
        $moduleids = explode(',', $request->moduleids);
        $floors = $request->FLOORS;
        $modules = $request->MODULES;
        // Set location of the new user
        foreach ($floorids as $floorid) {
            $authLocation = new AuthLocation;
            $authLocation->USER_ID = $userid;
            $authLocation->FLOOR_ID = $floorid;
            $authLocation->save();
        }
        // Set module of the new user
        if($request->hasModule == 1){
            foreach ($moduleids as $moduleid) {
                $authModule = new AuthModule;
                $authModule->USER_ID = $userid;
                $authModule->MODULE_ID = $moduleid;
                $authModule->save();
            }
        }
        // Save the new created user to DB
        return 'saved';
    }
    /**
     * <Layer number> (6.0) Disable the account of the user.
     * <Processing name> disableUser
     * <Function> Disable the active account
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return save()
     */
    public function disableUser(Request $request){
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'User Management';
        $instruction = 'Disabled a User';
        $this->auditLogs($ip,$host,$module,$instruction);
        // Function to disable the user
        $user = User::where('USER_ID',$request->USERID)->first();
        $user->REG_FLAG = 0;
        $user->save();
    }
    /**
     * <Layer number> (7.0) Enable the account of the user.
     * <Processing name> enableUser
     * <Function> Enable the deactivated account
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return save()
     */
    public function enableUser(Request $request){
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'User Management';
        $instruction = 'Enabled a User';
        $this->auditLogs($ip,$host,$module,$instruction);
        // Function to enable the user
        $user = User::where('USER_ID',$request->USERID)->first();
        $user->REG_FLAG = 1;
        $user->save();
    }
    
     /**
     * <Layer number> (8.0) Change the password of the user.
     * <Processing name> changePasswordUser
     * <Function> Change the user password
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return save()
     */
    public function changePasswordUser(Request $request){
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'User Management';
        $instruction = 'Changed User Password';
        try{
            abort(404, 'mekato');
        $this->auditLogs($ip,$host,$module,$instruction);
        // Function for creating new password
        $password = Hash::make($request->PASSWORD);
        // Save the new created password
        $user = User::where('USER_ID',$request->USERID)->first();
        $user->PASSWORD = $password;
        $user->save();
        }catch(\Throwable $e){
            $this->auditLogs($ip,$host,$module,$e->getMessage());
            return 'failed';
        }       
    }
    /**
     * <Layer number> (9.0) Update the profile of the user.
     * <Processing name> updateUserProfile
     * <Function> Update user profile
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return save()
     */
    public function updateUserProfile(Request $request){
        // For audit logs
        $ip = $request->ip();
        $authUser = auth()->user();
        $host = $authUser->USERNAME;
        $module = 'User Management';
        $instruction = 'Updated a Profile';
        $this->auditLogs($ip,$host,$module,$instruction);

        // Handle File Upload
        $userLogoPath = "storage/app/public/users";
        $file = $request->file;
        $fileName = $request->fileName;
        $filePath = '/'.$userLogoPath.'/'.$fileName;
        // Requesting field for updating profile
        $userid = $request->USERID;
        $username = $request->USERNAME;
        $useremail = $request->USEREMAIL;

        $users = User::where('USER_ID',$userid)->first();
        
        // Handle the Username and Email of the User
        $users->USERNAME = $username;
        $users->EMAIL = $useremail;
        if($request->hasFile == 1){
            $file->move(public_path($userLogoPath),$fileName);
            $users->USER_LOGO = $filePath;
        }
        // Save to DB
        $users->save();

    }
     /**
     * <Layer number> (10.0) Update the designation of the user.
     * <Processing name> updateUserDesignation
     * <Function> Update user designation
     *            URL: http://localhost/users
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return save()
     */
    public function updateUserDesignation(Request $request){
        // For audit logs
        $ip = $request->ip();
        $authUser = auth()->user();
        $host = $authUser->USERNAME;
        $module = 'User Management';
        $instruction = 'Updated User Designation';
        $this->auditLogs($ip,$host,$module,$instruction);
        // Requesting field for updating user designation
        $userid = $request->USERID;
        $floors = $request->FLOORS;
        $modules = $request->MODULES;

        $users = User::where('USER_ID',$userid)->first();
        // Location
        $users = AuthLocation::where('USER_ID',$userid)->get();
        // Delete the existing designation of the user
        foreach ($users as $user) {
            $user->delete();
        }
        // Set the new designation floor of the user
        foreach ($floors as $floor) {
            $authLocation = new AuthLocation;
            $authLocation->USER_ID = $userid;
            $authLocation->FLOOR_ID = $floor['FLOOR_ID'];
            $authLocation->save();
        }

        // Modules
        $users = AuthModule::where('USER_ID',$userid)->get();
        // Delete  the existing module of the user
        foreach ($users as $user) {
            $user->delete();
        }
        // Set the new designation module of the user
        foreach ($modules as $module) {
            $authModule = new AuthModule;
            $authModule->USER_ID = $userid;
            $authModule->MODULE_ID = $module['MODULE_ID'];
            $authModule->save();
        }
    }
}
