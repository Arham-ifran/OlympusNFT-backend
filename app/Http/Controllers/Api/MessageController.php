<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\MessageThreads;
use App\Models\Messages;
use App\Models\Templates;
use App\Http\Resources\MessageThreadsResource;
use App\Http\Resources\MessagesResource;
use App\Http\Resources\UserResource;
use Hash;
use Hashids;
use Mail;
use App\Mail\MasterMail;
use View;
use DB;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * SEND MESSAGE TO USER
     *
     * @param  [integer] sender_id
     * @param  [integer] receiver_id
     * @return [string] message
     * @return \Illuminate\Http\JsonResponse
     */

    public function sendMessage(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'sender_id' => 'required',
            'receiver_id' => 'required',
            'message' => 'required',
        );



        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }
        if (decodeApiIds($input["sender_id"]) != 0 && decodeApiIds($input["receiver_id"]) != 0) {
            $sender_id = decodeApiIds($input["sender_id"]);
            $receiver_id = decodeApiIds($input["receiver_id"]);


            $user = User::where('id', $sender_id)->where('is_active', 1)->first();
            if ($user) {

                $message_thread = MessageThreads::where('id', $request->input('thread_id'))
                    ->first();

                if ($message_thread) {

                    DB::beginTransaction();
                    try {
                        $message = Messages::create(['sender_id' => $user->id, 'receiver_id' => $receiver_id, 'thread_id' => $message_thread->id, 'message' => $request->input('message'), 'is_private' => 1, 'is_read' => 0, 'is_admin' => 0, 'is_dispute' => 0,]);
                        DB::commit();
                        // if ($message->receiver->email_notification == 1) {
                        /****send Email*****/
                        $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_message_sent')->first();
                        if ($template != '') {

                            $subject = $template->subject;

                            $link = env("FRONT_BASE_URL") . 'messages';
                            $to_replace = ['[RECEIVER]', '[SENDER]', '[LINK]', '[SITE_NAME]', '[SITE_URL]'];
                            $with_replace = [$message->receiver->username, $user->username, $link, SITE_NAME, env("FRONT_BASE_URL")];
                            $header = $template->header;
                            $footer = $template->footer;
                            $content = $template->content;

                            $html_header = str_replace($to_replace, $with_replace, $header);
                            $html_footer = str_replace($to_replace, $with_replace, $footer);
                            $html_body = str_replace($to_replace, $with_replace, $content);

                            $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                            Mail::queue(new MasterMail($message->receiver->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                        }
                        // }
                        /****end****/


                        return response()
                            ->json(['data' => array(
                                'thread_id' => $message_thread->id,
                                'message_id' => $message->id,
                            ), 'status' => 1, 'message' => trans('api.Your message has been created successfully.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 0, 'message' => trans('api.Oops something went wrong. Unable to create your account.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    }
                } else {


                    $product_id =  $request->has('product_id') && $request->product_id != "" ? $input["product_id"] : NULL;
                    DB::beginTransaction();
                    try {
                        $message_thread = MessageThreads::create([
                            'sender_id' => $user->id, 'receiver_id' => $receiver_id, 'product_id' => $product_id,

                        ]);

                        $message = Messages::create(['sender_id' => $user->id, 'receiver_id' => $receiver_id, 'thread_id' => $message_thread->id, 'message' => $request->input('message'), 'is_private' => 1, 'is_read' => 0, 'is_admin' => 0, 'is_dispute' => 0,]);
                        DB::commit();
                        //if ($message->receiver->email_notification == 1) {
                        /****send Email*****/
                        $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_message_sent')->first();
                        if ($template != '') {

                            $subject = $template->subject;
                            $link = env("FRONT_BASE_URL") . 'messages';
                            $to_replace = ['[RECEIVER]', '[SENDER]', '[LINK]', '[SITE_NAME]', '[SITE_URL]'];
                            $with_replace = [$message->receiver->username, $user->username, $link, SITE_NAME, env("FRONT_BASE_URL")];
                            $header = $template->header;
                            $footer = $template->footer;
                            $content = $template->content;

                            $html_header = str_replace($to_replace, $with_replace, $header);
                            $html_footer = str_replace($to_replace, $with_replace, $footer);
                            $html_body = str_replace($to_replace, $with_replace, $content);

                            $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                            Mail::queue(new MasterMail($message->receiver->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                        }
                        /****end****/
                        //}
                        return response()
                            ->json(['data' => array(
                                'thread_id' => $message_thread->id,
                                'message_id' => $message->id,
                            ), 'status' => 1, 'message' => trans('api.Your message has been created successfully', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 0, 'message' => trans('api' . $e->getMessage(), array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    }
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
        }
    }


    /**
     * FETCH MESSAGE THREADS
     * @param  [integer] user_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function fetchMessageThreads(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id' => 'required',

        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {


                $messagethreads = MessageThreads::where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
                $total_messagethreads =  $messagethreads->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $messagethreads = $messagethreads->offset($offset)->limit($limit)->orderBy('id', 'DESC')
                    ->get();


                if (!$messagethreads->isEmpty()) {

                    return MessageThreadsResource::collection($messagethreads)->additional(
                        [
                            'total_records' => $total_messagethreads,
                            'message' => trans('api.Message Thread Listing', array(), $app_language),
                            'status' => 1
                        ]
                    );
                } else {

                    return response()
                        ->json([
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]);
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
     * FETCH USER MESSAGES
     *
     * @param  [integer] user_id
     * @param  [string] thread_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function fetchMessages(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id' => 'required',
            'thread_id' => 'required|int',

        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {

                $user_id = $user->id;
                $messages = Messages::where('thread_id', $request->thread_id)
                    ->where(function ($query) use ($user_id) {
                        $query->where('sender_id', $user_id)
                            ->orWhere('receiver_id', $user_id);
                    });
                $total_messages =  $messages->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $messages_result = $messages->offset($offset)->limit($limit)->orderBy('id', 'DESC')
                    ->get();

                if (!$messages_result->isEmpty()) {
                    $update_read_status = Messages::where('thread_id', $request->thread_id);
                    $update['is_read'] = 1;
                    $update_read_status->update($update);

                    $messages_result = $messages_result->toArray();
                    $messages = array_map(function ($object) {
                        return (array) $object;
                    }, $messages_result);
                    aasort($messages, 'id');
                    return MessagesResource::collection($messages)->additional([
                        'total_records' => count($messages_result),
                        'message' => trans('api.Message Listing', array(), $app_language),
                        'status' => 1
                    ]);
                } else {

                    return response()
                        ->json([

                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]);
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
     * SEARCH USER FOR SENDING MESSAGE
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUsersForMessage(Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'search_user' => 'required',
            'senderId' => 'required'

        );
        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }


        if (decodeApiIds($input["senderId"]) != 0) {
            $user = User::where('id', decodeApiIds($input["senderId"]))->where('is_active', 1)->first();
            if ($user) {
                $messagethreads = MessageThreads::where('sender_id', $user->id);
                $pluckedReceiver = $messagethreads->pluck('receiver_id');
                $pluckedReceiverIds = $pluckedReceiver->all();
                $message_user = User::where('is_active', 1)->whereNotIn('id', $pluckedReceiverIds);

                if ($request->has('search_user') && $request->search_user <> '') {
                    $message_user->where('username', 'LIKE', '%' . $request->search_user . '%');
                }

                $message_user = $message_user->get();


                if (!$message_user->isEmpty()) {
                    return UserResource::collection($message_user)->additional([
                        'total_records' => count($message_user),
                        'message' => trans('api.message user all records successfully.', array(), $app_language),
                        'status' => 1
                    ]);
                } else {

                    return response()->json(['status' => 0, 'message' => trans('api.The User you are trying to access not present.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
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
}
