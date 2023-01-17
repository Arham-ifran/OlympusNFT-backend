<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageThreads;
use Illuminate\Http\Request;
use DataTables;
use Alert;

class MessagesController extends Controller
{

    use \App\Traits\SendMessageTrait;

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = array();

        if ($request->ajax()) {
            $db_record = MessageThreads::with(['product', 'messages' => function ($q) {
                return $q->where(['is_private' => 0, 'is_dispute' => 0]);
            }])->orderByDesc('id')->get();

            $datatable = DataTables::of($db_record);

            $datatable->addColumn('seller', function ($row) {
                return $row->messages->first()->sender->full_name();
            });
            $datatable->addColumn('product_name', function ($row) {
                return $row->product->product_name;
            });
            $datatable->addColumn('buyer', function ($row) {
                return $row->messages->first()->receiver->full_name();
            });

            $datatable->addColumn('message', function ($row) {
                return $row->messages->last()->message;
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/messages/" . encode($row->product_id) . '/view') . '" title="View Messages"><i class="fa fa-envelope"></i></a>';

                return $actions;
            });

            $datatable = $datatable->rawColumns(['product_name', 'seller', 'buyer', 'message', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.messages.index')->with($data);
    }

    /**
     * view
     *
     * @param  mixed $id
     * @return void
     */
    public function view($id)
    {
        $data = array();

        $product_id = decode($id);

        if ($product_id) {

            $data['current_thread']  = MessageThreads::with(['messages' => function ($q) {
                return $q->where('is_dispute', 0);
            }])
                ->where('product_id', $product_id)
                ->orderByDesc('id')
                ->first();
            $data['thread_id'] = $data['current_thread']->id;

            $data['seller'] = $data['current_thread']->messages->first()->sender->full_name();
        } else {
            Alert::error('Error', 'Messages not found.')->persistent('Close')->autoclose(5000);
            return redirect('admin/messages');
        }

        return view('admin.messages.view')->with($data);
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function send_message(Request $request)
    {

        $user = auth()->user();
        $input = $request->all();
        $input['is_admin'] = 1;
        $input['sender_id'] = $user->id;
        $input['is_private'] = 0;
        $input['is_dispute'] = 0;
        $response = $this->sendMessageFromTrait(null, $input, $user, 1);

        //EVENT LOG START
        $meta = ['item_id' => $input['id'], 'thread_id' => $input['thread_id'], 'message_id' => $response['id']];
        \App\Events\UserEvents::dispatch('admin', 29, $response['message'], $meta);

        // return response()->json($response, 200, ['Content-Type' => 'application/json']);
        if ($response['status'] == 0) {
            Alert::error('Error', $response['message'])->persistent('Close')->autoclose(5000);
        } else {
            Alert::success('Success', $response['message'])->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/messages/' . $input['id'] . '/view');
    }
}
