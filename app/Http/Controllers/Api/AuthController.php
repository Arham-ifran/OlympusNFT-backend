<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Transactions;
use Carbon\Carbon;
use Hashids;
use Storage;
use Image;
use Hash;
use DB;
use App\Http\Resources\SellerProfileResource;
use App\Http\Resources\TransactionResource;
use Intervention\Image\Exception\NotReadableException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'verifyAccount', 'resendVerificationEmail', 'checkUser', 'sellerProfile']]);
    }

    /**
     * REGISTER USER.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $messages = ['username.unique' => 'This username already exists.', 'email.unique' => 'This email address already exists.',];

        $validation_rules = array(
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'confirm_email ' => 'same:email',
            'password' => 'required|string|min:8',
            'email_notification' => 'required',
            'accept' => 'required|accepted',
            'user_type' => 'required'


        );

        $validator = Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'email_notification' => $request->input('email_notification'),
            'user_type' => $request->input('user_type'),
            'is_active' => 1,
        ]);

        if ($user) {
            $credentials = ['email' => $request->email, 'password' => $request->password];
            $token = auth()->attempt($credentials);

            // ************************* //
            // Send Verify Link To User
            // ************************* //


            // $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'register_an_account')->first();
            // if ($template != '') {
            //     $subject = $template->subject;
            //     $link = url('/verify-account/'.Hashids::encode($user->id));
            //     $to_replace = ['[USERNAME]', '[EMAIL]', '[LINK]','[APP_NAME]'];
            //     $with_replace = [ $input['username'], $input['email'],$link,env('APP_NAME')];
            //     $header = $template->header;
            //     $footer = $template->footer;
            //     $content = $template->content;
            //     $html_header = str_replace($to_replace, $with_replace, $header);
            //     $html_footer = str_replace($to_replace, $with_replace, $footer);
            //     $html_body = str_replace($to_replace, $with_replace, $content);
            //     $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
            //     Mail::queue(new MasterMail($input['email'], SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
            // }


            $user->user_id = Hashids::encode($user->id);
            return response()
                ->json(['data' => $user->makeHidden(['id', 'address', 'address2', 'city', 'state', 'country', 'zipcode', 'dob']), 'token' => $token, 'status' => 1, 'message' => trans("api.Your account has been created successfully.", array(), $app_language)], 200, ['Content-Type' => 'application/json']);
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Oops something went wrong. Unable to create your account.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $validator = Validator::make($request->all(), ['email' => 'required|string|email', 'password' => 'required|string']);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['status' => 0, 'message' => trans('api.Invalid Email or Password.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
        }

        $user = User::where(['email' => $request->input('email')])->first();

        $is_user_active = true;

        $message = '';

        switch ($user->is_active) {
            case 0:
                $message = trans("api.Please verify your email account. If you didn't receive verification email then resend email.", array(), $app_language);
                $is_user_active = false;
                break;

            case 2:
                $message = trans("api.Your account status has been block by Admin.", array(), $app_language);
                $is_user_active = false;
                break;
        }

        if ($is_user_active == false) {
            auth()->logout();
            return response()
                ->json(['email' => $request->email, 'status' => 0, 'user_status' => $user->is_active, 'message' => $message], 200, ['Content-Type' => 'application/json']);
        }

        // $user->update([
        //     'last_login' => Carbon::now('UTC')->timestamp,
        // ]);
        $user->user_id = Hashids::encode($user->id);
        return response()
            ->json([
                'token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()
                    ->getTTL(), 'data' => $user->makeHidden(['id', 'email_verified_at', 'remember_token']),

                'status' => 1, 'message' => trans("api.User has been logged in Successfully.", array(), $app_language),
            ], 200, ['Content-Type' => 'application/json']);
    }


    /**
     * GET USER PROFILE BY ID.
     *
     * @param  string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile($id, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (decodeApiIds($id) != 0) {
            $user = User::where('id', decodeApiIds($id))->where('is_active', 1)->first();
            if ($user) {
                return response()
                    ->json(['data' => $user->makeHidden(['id', 'photo']), 'status' => 1, 'message' => trans('api.Your Profile', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        auth()->logout();

        return response()
            ->json(['status' => 1, 'message' => trans('api.Successfully logged out', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        return response()->json(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()
            ->getTTL(), 'status' => 1, 'message' => trans('User Token', array(), $app_language)]);
    }

    /**
     * UPDATE USER PROFILE.
     *
     * @param  [integer] user_id
     * @param  [integer] username
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateProfile(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), ['username' => 'required|string|max:100', 'user_id' => 'required']);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()
                    ->all()], 400, ['Content-Type' => 'application/json']);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {
                DB::beginTransaction();
                try {
                    DB::commit();
                    $user->update($input);
                    if ($request->hasFile('banner_image')) {


                        $path = 'uploads/users';
                        if (!Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists($path)) {
                            Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->makeDirectory($path);
                        }
                        $destinationPath = 'uploads/users';
                        $file = $request->banner_image;
                        try {
                            $img = Image::make($file->getRealPath())->resize(1920, 1280, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        } catch (NotReadableException $e) {
                            return response()->json(['status' => 0, 'message' => trans('api.Error! Image Type Not Supported.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }

                        $image_name = 'user_banner_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                        Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))
                            ->put($destinationPath . '/' . $image_name, $img->stream()
                                ->__toString());
                        $user->banner_image = $image_name;
                        $user->save();
                    }

                    $user->user_id = Hashids::encode($user->id);
                    DB::commit();
                    return response()
                        ->json(['data' => $user->makeHidden(['id']), 'status' => 1, 'message' => trans("api.Profile has been updated successfully.", array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * VERIFY USER ACCOUNT.
     *
     * @param  string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyAccount($id, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (!isset(Hashids::decode($id)[0])) {
            return response()->json(['status' => 0, 'message' => trans('api.unable_verify_account', array(), $app_language)]);
        }

        $user = User::find(Hashids::decode($id)[0]);
        if ($user) {
            $user->update(['is_active' => 1]);

            $credentials = ['email' => $user->email, 'password' => $user->original_password];
            $token = auth()->attempt($credentials);

            $user->update([
                'last_login' => Carbon::now('UTC')->timestamp

            ]);

            return response()
                ->json(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()
                    ->getTTL(), 'data' => $user, 'status' => 1, 'message' => trans('api.verify_account', array(), $app_language)]);

            //return response()->json(['status' => 1, 'message' => 'Your account has been verified successfully.']);

        } else {
            return response()->json(['status' => 0, 'message' => trans('api.Oops something went wrong. Unable to verify your account.', array(), $app_language)]);
        }
    }

    /**
     * RESEND VERIFICATION CODE.
     *
     * @param  [string] email
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function resendVerificationEmail(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $user = User::where('email', $request->email)
            ->first();

        if ($user) {
            // ************************* //
            // Send Verify Link To User
            // ************************* //
            // $name = $user->name;
            // $email = $user->email;
            // $link = url('/verify-account/'.Hashids::encode($user->id));
            // $email_template = EmailTemplate::where('type','sign_up_confirmation')->first();
            // $email_template = transformEmailTemplateModel($email_template,$lang);
            // $subject = $email_template['subject'];
            // $content = $email_template['content'];
            // $search = array("{{name}}","{{link}}","{{app_name}}");
            // $replace = array($name,$link,env('APP_NAME'));
            // $content  = str_replace($search,$replace,$content);
            // sendEmail($email, $subject, $content, '', '', $lang);
            return response()->json(['status' => 1, 'message' => trans('api.We have e-mailed you Please check your inbox or spam folder.', array(), $app_language)]);
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.We can not find a user with that e-mail address.', array(), $app_language)]);
        }
    }

    /**
     * CHECK USE EXISTS
     *
     * @param  [string] email
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function checkUser(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $validator = Validator::make($request->all(), ['email' => 'required|string|email',]);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()
                    ->all()]);
        }

        $user = User::where(['email' => $request
            ->email])
            ->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => trans('api.We cannot find a user.', array(), $app_language)]);
        } else {
            return response()
                ->json(['data' => array(
                    'user' => $user
                ), 'status' => 1, 'message' => trans('api.User already exists.', array(), $app_language)]);
        }
    }

    /**
     * UPDATE USER WALLET ADDRESS
     *
     * @param  [integer] user_id
     * @param  [integer] wallet_address
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateWalletAddress(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), ['wallet_address' => 'required|string', 'user_id' => 'required']);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()
                    ->all()], 400, ['Content-Type' => 'application/json']);
        }
        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                DB::beginTransaction();
                try {
                    $user->update(['wallet_address' => $input["wallet_address"]]);
                    DB::commit();
                    return response()
                        ->json(['status' => 1, 'message' => trans('api.Wallet Address is updated succesfully', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {

            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * UPLOAD USER PROFILE IMAGE AND SAVE I DATABASE
     *
     * @param  REQUEST DATA
     * @param  [integer] user_id
     * @param  [integer] image
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadUserImage(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id' => 'required',
            'image' => 'file|image|mimes:jpg,jpeg,png,svg',
        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                if ($request->hasFile('image')) {
                    DB::beginTransaction();
                    try {
                        $path = 'uploads/users';
                        if (!Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists($path)) {
                            Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->makeDirectory($path);
                        }
                        $destinationPath = 'uploads/users';
                        $file = $request->image;
                        try {
                            $img = Image::make($file->getRealPath())->resize(1920, 1280, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        } catch (NotReadableException $e) {
                            return response()->json(['status' => 0, 'message' => trans('api.Error! Image Type Not Supported.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }

                        $image_name = 'user_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                        Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))
                            ->put($destinationPath . '/' . $image_name, $img->stream()
                                ->__toString());
                        $user->photo = $image_name;
                        $user->save();
                        DB::commit();
                        return response()
                            ->json([
                                'status' => 1, 'data' => array(
                                    'image_path' => asset('storage/uploads/users/' . $user->photo),
                                ),

                                'message' => trans("api.User image uploaded successfully.", array(), $app_language)
                            ], 200, ['Content-Type' => 'application/json']);
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                    }
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {

            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
    /**
     * UPDATE LOGIN USER PASSWORD
     *
     * @param  [string] email
     * @param  [string] password
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function updatePassword(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string', 'new_password' => 'required|string|min:8', 'user_id' => 'required'

        ]);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()
                    ->all()], 400, ['Content-Type' => 'application/json']);
        }
        if (decodeApiIds($request->input('user_id')) != 0) {
            $user = User::where('id', decodeApiIds($request->input('user_id')))->where('is_active', 1)->first();
            if ($user) {

                if (Hash::check($request->input('current_password'), $user->password)) {
                    DB::beginTransaction();
                    try {
                        $change_password = $user->update([
                            'password' => Hash::make($request->input('new_password')),

                        ]);
                        if ($change_password) {
                            DB::commit();
                            return response()->json(['status' => 1, 'message' => trans('api.Password has been Change successfully', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['status' => 0, 'message' => $e->getMessage()], 500, ['Content-Type' => 'application/json']);
                    }
                } else {

                    return response()
                        ->json(['status' => 0, 'message' => trans('api.Current password is not correct.', array(), $app_language)], 400, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {

            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }


    public function sellerProfile($user_name, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        if ($user_name) {
            $user = User::where('username', $user_name)->where('is_active', 1)->first();

            if ($user) {
                return (new SellerProfileResource($user))
                    ->additional([
                        // 'additional' => ,
                        'message' => 'Seller Profile',
                        'status'  => 1
                    ]);
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.No record found', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => trans('api.No record found', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * User Transactions History.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */

    public function userTransactions(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                $transactions = Transactions::where('is_active', 1)->where('user_id', $user->id);

                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $transactions = $transactions->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$transactions->isEmpty()) {
                    $total_transactions =  $transactions->count();
                    return TransactionResource::collection($transactions)
                        ->additional([
                            'total_records' => $total_transactions,
                            'message' => trans('api.Transaction Listing', array(), $app_language),
                            'status'  => 1
                        ]);
                } else {

                    return response()->json(
                        [
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]
                    );
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
}
