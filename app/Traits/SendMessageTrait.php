<?php

namespace App\Traits;

use App\Models\Messages;
use App\Models\MessageThreads;
use App\Events\MessageSent;
use App\Models\Templates;
use App\Models\User;
use View;
use Mail;
use App\Mail\MasterMail;

trait SendMessageTrait
{

    public function sendMessageFromTrait($user = null, $input, $admin = null, $sendEMail = 0)
    {

        $thread = MessageThreads::findOrFail($input['thread_id']);
        if ($thread) {
            try {

                if ($input['is_private'] == 1) {
                    $message = encryptText($input['message']);
                } else {
                    $message = $input['message'];
                }

                $messages = Messages::create(
                    [
                        'thread_id' => $input['thread_id'],
                        'sender_id' => $input['sender_id'],
                        'receiver_id' => $input['receiver_id'],
                        'message' => $message,
                        'is_private' => $input['is_private'],
                        'is_admin' => $input['is_admin'],
                        'is_dispute' => $input['is_dispute']
                    ]
                );
                if ($input['is_private'] == 0) {
                    // $channel = 'OlympusNFT-art-' . $input['thread_id'];
                    // broadcast(new MessageSent($user, $messages, $channel, $admin))->toOthers();
                }

                $receiver = User::findOrFail($input['receiver_id']);
                if ($receiver && $sendEMail == 1) {

                    if ($input['is_dispute'] == 1) {
                        $email_type = 'send_message_dispute';
                    } else {
                        $email_type = 'send_message';
                    }

                    $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', $email_type)->first();
                    if ($template != '') {
                        $link = url('/messages/' . encode($thread->product_id));
                        $subject = $template->subject;
                        $to_replace = ['[FIRSTNAME]', '[LASTNAME]', '[MESSAGE]', '[LINK]'];
                        $with_replace = [$receiver->firstname, $receiver->lastname, $input['message'], $link];
                        $header = $template->header;
                        $footer = $template->footer;
                        $content = $template->content;
                        $html_header = str_replace($to_replace, $with_replace, $header);
                        $html_footer = str_replace($to_replace, $with_replace, $footer);
                        $html_body = str_replace($to_replace, $with_replace, $content);

                        $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                        
                        Mail::queue(new MasterMail($receiver->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                    }
                }
                if ($input['is_private'] == 1) {
                    // return redirect('messages?tab=private');
                    return ['status' => 2, 'message' => 'Message sent successfully!', 'id' => $messages->id];
                }

                return ['status' => 1, 'message' => 'Message sent successfully!', 'id' => $messages->id];
            } catch (\Exception $e) {

                return ['status' => 0, 'message' => $e->getMessage(), 'id' => 0];
            }
        } else {
            return ['status' => 0, 'message' => 'You are not allowed to send Message.', 'id' => 0];
        }
    }
}
