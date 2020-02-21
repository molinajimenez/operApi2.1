<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');
        
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required'
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        
        $user = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        $user->assignRole('guest');
        $verification_code = str_random(30); //Generate verification code
        DB::table('user_verifications')->insert(['user_id'=>$user->id,'token'=>$verification_code]);

        $subject = "Please verify your email address.";
        Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code],
            function($mail) use ($email, $name, $subject){
                $mail->from(getenv('MAIL_USERNAME'), "Operwork System");
                $mail->to($email, $name);
                $mail->subject($subject);
            });

        return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token',$verification_code)->first();

        if(!is_null($check)){
            $user = User::find($check->user_id);

            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'Account already verified..'
                ]);
            }

            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token',$verification_code)->delete();

            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully verified your email address.'
            ]);
        }

        return response()->json(['success'=> false, 'error'=> "Verification code is invalid."]);

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 401);
        }
        
        $credentials['is_verified'] = 1;
        
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 404);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }

        // all good so return the token
        return response()->json(['success' => true, 'token' => $token ], 200);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     */
    public function logout(Request $request) {
        $this->validate($request, ['token' => 'required']);
        
        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        try {
            Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject('Your Password Reset Link');
            });

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }

        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function edit(Request $request){
        $user = User::where('email', $request->json()->get('email'))->first();

        $info = $request->json()->all();
        /*
        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $ext;
            $file->move('uploads/profiles/', $fileName);
            $img = $fileName;
        } else{
            $img = null;
        }
        */
        if($user != null){
            $user->fill(
                $info
            )->save();
        } else{
            return response()->json(['status' => 'User does not exist'], 400);
        }

        return response()->json(['status' => 'Updated user'], 200);

    }

    public function userPermission(){
        $values = auth()->user();

        $allPermissions = DB::table('permissions')->get();
        //aqui ya tengo el rol asociado a mi usuario loggeado
        $model_roles = DB::table('model_has_roles')->where('model_id',$values->id)->first();
        //esto es una lista de permisos del rol
        $existingPermissions = DB::table('role_has_permissions')->where('role_id', $model_roles->role_id)->get();
        $responseArray = [];
        $lookedIds = [];
        $roles = DB::table('roles')->where('id', $model_roles->role_id)->get();
        $roles_response = [];

        foreach($roles as $role){
            foreach ($allPermissions as $elem) {
                if (!in_array($elem->id, $lookedIds)) {
                    $perm = DB::table('permissions')->where('id', $elem->id)->first();
                    array_push($lookedIds, $elem->id);
                    array_push($responseArray, ['permission_id' => $elem->id, "name" => $perm->name, 'value' => false]);
                }
            }
    
            foreach ($existingPermissions as $existingElem) {
                foreach ($responseArray as $responseElem => $value) {
                    if ($existingElem->permission_id === $responseArray[$responseElem]['permission_id']) {
                        $responseArray[$responseElem]['value'] = true;
                    }
                }
            }

            $obj = [
                'id' => $role->id,
                'role' => $role->name,  //por ahora, despues pasamos nombre bonito.
                'permissions' => $responseArray
    
            ];

            array_push($roles_response, $obj);
        }
        
        return response()->json($roles_response, 200);
    }
}