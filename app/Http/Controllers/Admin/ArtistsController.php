<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Countries;
use Illuminate\Http\Request;
use Alert;
use Image;
use Hash;
use File;
use View;
use DataTables;
use Form;
use App\Models\Templates;
use Mail;
use App\Mail\MasterMail;
use Illuminate\Support\Facades\Validator;
use Auth;

class ArtistsController extends Controller
{


    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Artists')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data = User::where('user_type', 2)->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('name', function ($row) {
                $admin_name = $row->firstname . ' ' . $row->lastname;
                return $admin_name;
            });


            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                elseif ($row->is_active == 0)
                    return '<label class="badge badge-warning">Inactive</label>';
                elseif ($row->is_active == 2)
                    return '<label class="badge badge-danger">Block</label>';
                else
                    return '<label class="badge badge-danger">Deleted</label>';
            });

            $datatable = $datatable->editColumn('total_products', function ($row) {
                if (count($row->artists_products->where('price_type', 0)) > 0)

                return '<label class="badge badge-success">' . count($row->artists_products->where('price_type', 0)) .'</label';

                else
                    return '<label class="badge badge-light">' . count($row->artists_products->where('price_type', 0)) . '</label>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (Auth::user()->can('Edit Artists')) {
                    $actions .= Form::open([
                       'method' => 'POST',
                       'url' => ['admin/artists/update-status'],
                       'style' => 'display:table;margin-right:10px;',
                       'class' => 'float-sm-right',
                       'id' => 'statusForm' . $row->id
                   ]);
                    $actions .= Form::hidden('id', encode($row->id));

                    $actions .= Form::select('is_active', [
                       '0' => 'Inactive',
                       '1' => 'Active',
                       '2' => 'Block',
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= Form::close();
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/artists/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/artists/" . encode($row->id)) . '" title="artists view" target="_blank"><i class="fa fa-eye"></i></a>';
                }
                if(Auth::user()->can('Delete Artists')){
                    $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/artists', encode($row->id)],
                    'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Artist"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }

                return $actions;
            });



            $datatable = $datatable->rawColumns(['role', 'name', 'total_products','status', 'approved_status', 'action', 'approve_etl']);
            return $datatable->make(true);
        }

        return view('admin.artists.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Artists')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";
        $data['countries'] = Countries::where('is_active', 1)->get();

        if (old()) {
            $data['artist'] = old();
        }

        return view('admin.artists.form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $input['user_type'] = 2;
        if ($input['action'] == 'Edit') {
            $user = User::findOrFail($input['id']);



            $this->validate($request, [
                'username' => ['required', 'string',   'max:20'],
                'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
                

            ]);
            if (!empty($input['password']) && $input['password_confirmation'] <> null && $input['password'] <> null) {
                $this->validate($request, [
                    'password' => 'required|string|min:8|confirmed'
                ]);
                $input['password'] =  Hash::make($input['password']);
            } else {
                unset($input['password']);
            }
           
            $User = User::findOrFail($input['id']);

            $User->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 25, '', ['item_id' => $User->id]);
            Alert::success('Success', 'Artist updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'username' => ['required', 'string',   'max:20'],
                'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
                'password' => 'required|string|min:8|confirmed',
            ]);

            $input['password'] = Hash::make($input['password']);

            $User = User::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 10, '', ['item_id' => $User->id]);
            $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'create_buyer_admin')->first();
            if ($template != '') {

                $subject = $template->subject;

                $link = url("admin/login");
                $to_replace = ['[FIRSTNAME]', '[LASTNAME]', '[EMAIL]', '[PASSWORD]', '[LINK]'];
                $with_replace = [$input['firstname'], $input['lastname'], $input['email'], $input['password_confirmation'], $link];
                $header = $template->header;
                $footer = $template->footer;
                $content = $template->content;
                $html_header = str_replace($to_replace, $with_replace, $header);
                $html_footer = str_replace($to_replace, $with_replace, $footer);
                $html_body = str_replace($to_replace, $with_replace, $content);

                $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();

                Mail::queue(new MasterMail($input['email'], SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
            }
            Alert::success('Success', 'Artist added successfully.')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/artists');
    }

     /**
     * Artist Details.
     *
     * @param  \App\Models\User
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decode($id);
        $data['artist']  = User::findOrFail($id);

        return view('admin.artists.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Artists')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['artist'] = User::findOrFail($id);
        $data['countries'] = Countries::where('is_active', 1)->get();
        $data['action'] = "Edit";
        return view('admin.artists.form')->with($data);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Artists')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        $data = array(
            'is_active' => 2,
        );

        User::whereId($id)->delete($data);

        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 26, '', ['item_id' => $id]);
        Alert::success('Success', 'Artist deleted successfully!')->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }

    // Update Status
    public function update_status(Request $request)
    {
        $input = $request->all();

        $is_active = $input['is_active'];
        $id = decode($input['id']);

        if ($is_active <> '' && $id <> '') {
            $data = array(
                'is_active' => $is_active,
            );

            User::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 27, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
