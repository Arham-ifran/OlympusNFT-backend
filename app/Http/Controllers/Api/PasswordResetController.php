<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Templates;
use App\Models\PasswordReset;
use Hash;
use View;
use Mail;
use App\Mail\MasterMail;


class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function sendResetLink(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }



        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => trans("api.We can't find a user with that e-mail address.",array(), $app_language)
            ]);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => \Str::random(60)
            ]
        );

        if ($user && $passwordReset) {


            // ********************************* //
            // Send Forgot Paasword Email To User
            // *********************************** //


            $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'forgot_password')->first();
            if ($template != '') {

                $subject = $template->subject;

                $link = env("FRONT_BASE_URL").'resetPassword/find/' . $passwordReset->token;
                $to_replace = ['[USERNAME]','[LINK]','[SITE_NAME]','[SITE_URL]'];
                $with_replace = [$user['username'], $link,SITE_NAME,env("FRONT_BASE_URL")];
                $header = $template->header;
                $footer = $template->footer;
                $content = $template->content;
                $html_header = str_replace($to_replace, $with_replace, $header);
                $html_footer = str_replace($to_replace, $with_replace, $footer);
                $html_body = str_replace($to_replace, $with_replace, $content);

                $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
               
                Mail::queue(new MasterMail($input['email'], SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
            }



            return response()->json([
                'status' => 1,
                'message' =>  trans('api.We have e-mailed you reset password link! Please check your inbox or spam folder.', array(), $app_language)
            ], 200, ['Content-Type' => 'application/json']);
        }
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function validateResetToken($token, Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json([
                'status' => 0,
                'message' =>  trans('api.This password reset token is invalid', array(), $app_language)
            ], 404, ['Content-Type' => 'application/json']);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(60)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'status' => 0,
                'message' =>  trans('api.This password reset token is invalid', array(), $app_language)
            ], 404, ['Content-Type' => 'application/json']);
        }

        return response()->json([
            'data' => $passwordReset,
            'status' => 1,
            'message' =>  trans('api.This password reset token is valid.', array(), $app_language)
        ], 200, ['Content-Type' => 'application/json']);
    }
    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $validator = Validator::make($request->all(), [
            //'email' => 'required|string|email|max:191',
            'password'  => 'required|string|min:8|max:30|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
            'password_confirmation' => 'same:password',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }
        //['email', $request->email]
        $passwordReset = PasswordReset::where([
            ['token', $request->token]
            
        ])->first();

        if (!$passwordReset) {
            return response()->json([
                'status' => 0,
                'message' =>  trans('api.This password reset token is invalid.', array(), $app_language),
            ]);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' =>  trans("api.We can't find a user with that e-mail address.", array(), $app_language)
            ]);
        }


        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();

        return response()->json([
            'data' => $user,
            'status' => 1,
            'message' =>  trans("api.Your password has been updated successfully.", array(), $app_language),
        ], 200, ['Content-Type' => 'application/json']);
    }
}
