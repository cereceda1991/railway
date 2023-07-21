<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public function passwordResetProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }
  
      // Verify if token is valid
    private function updatePasswordRow($request){
        return PasswordReset::where([
         'email' => $request->email,
         'token' => $request->resetToken
        ]);
    }
  
    // Token not found response  
    private function tokenNotFoundError() {
      return response()->json([
        'error' => 'Either your email or token is wrong.'
      ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  
    // Reset password
    private function resetPassword($request) {
        // find email
        $userData = User::whereEmail($request->email)->first();
        // update password
        $userData->update([
            'password'=>bcrypt($request->password)
        ]);
          // remove verification data from db
        $this->updatePasswordRow($request)->delete();
  
          // reset password response
        return response()->json([
            'data'=>'Password has been updated.'
          ],Response::HTTP_CREATED);
    }    
}
