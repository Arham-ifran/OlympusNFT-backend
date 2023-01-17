<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use DataTables;
use Alert;
use App\Mail\MasterMail;
use App\Models\Templates;
use Form;
use Illuminate\Support\Facades\Mail;
use View;
use DB;
use Auth;
class ContactUsController extends Controller
{

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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Contact Us Log')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = ContactUs::orderBy('id','desc')->get();

            $datatable = DataTables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->editColumn('date', function ($row) {
                return date('M d,Y', strtotime($row->created_at));
            });

            $datatable = $datatable->editColumn('is_replied', function ($row) {
                if ($row->is_replied == 0) {
                    $is_replied = 'Pending';
                } else {
                    $is_replied = 'Already replied';
                }
                return $is_replied;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';
                if(Auth::user()->can('Delete Contact Us Log')){
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contactus-log', encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Activity Log"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::close();

            }
                $actions .= '</span>';
            if(Auth::user()->can('Detail Contact Us Log')){

                $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/contactus-log/detail/" . encode($row->id)) . '" title="Reply"><i class="fa fa-eye"></i></a>';
            }
            return $actions;
            });

            $datatable = $datatable->rawColumns(['date', 'action', 'is_replied']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.contactus.index')->with($data);
    }

    public function destroy($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Contact Us Log')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        if ($id) {
            ContactUs::where('id', $id)->delete();
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 23, '', ['item_id' => $id]);
            Alert::success('Success', 'Contact Us log deleted successfully!')->persistent('Close')->autoclose(5000);
            return redirect('admin/contactus-log');
        } else {
            return redirect('admin/contactus-log');
        }
    }

    public function detail($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Detail Contact Us Log')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $id = decode($id);
        if ($id) {
            $data['contact'] = ContactUs::where('id', $id)->first();

            return view('admin.contactus.view')->with($data);
        } else {
            return redirect('admin/contactus-log');
        }
    }

    public function reply(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Reply Contact Us Log')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        if ($request->has('message')) {

            if ($request->all()) {

                $validation = $request->validate([
                    'message' => ['required', 'string',  'max:500']
                ]);

                DB::beginTransaction();
                try {
                    $input = $request->all();
                    $contact = ContactUs::where('id', $input['id'])->first();

                    $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'contact_us_reply')->first();
                    if ($template != '') {

                        $subject = $template->subject;
                        $to_replace = ['[NAME]', '[MESSAGE]'];
                        $with_replace = [$contact->fullname, nl2br(removeUrls(removeHtml($input['message'])))];
                        $header = $template->header;
                        $footer = $template->footer;
                        $content = $template->content;
                        $html_header = str_replace($to_replace, $with_replace, $header);
                        $html_footer = str_replace($to_replace, $with_replace, $footer);
                        $html_body = str_replace($to_replace, $with_replace, $content);

                        $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                        Mail::queue(new MasterMail($contact->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                    }
                    $contact->is_replied = 1;
                    $contact->save();
                    //EVENT LOG START
                    \App\Events\UserEvents::dispatch('admin', 24, '', ['item_id' => $contact->id]);

                    DB::commit();

                    Alert::success('Success', 'Email sent to user successfully.')->persistent('Close')->autoclose(5000);
                    return redirect()->back();
                } catch (\Exception $e) {
                    DB::rollback();
                    Alert::error('Error', $e->getMessage())->persistent('Close')->autoclose(5000);

                    return redirect()->back()->withErrors($validation)->withInput();
                }
            }
        }
    }
}
