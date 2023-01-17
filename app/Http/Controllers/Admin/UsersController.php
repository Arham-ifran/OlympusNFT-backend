<?php

namespace App\Http\Controllers\Admin;

use Auth;
use File;
use Form;
use Hash;
use Mail;
use View;
use Alert;
use Image;
use DataTables;
use App\Models\Admin;
use App\Mail\MasterMail;
use App\Models\Countries;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Templates;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{


    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // function __construct()
    // {
    //     $this->middleware('permission:user-create', ['only' => ['create','store']]);
    // }
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Admin Users')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data = Admin::all();

            $datatable = Datatables::of($data);
            $datatable->editColumn('name', function ($row) {
                $admin_name = $row->firstname . ' ' . $row->lastname;
                return $admin_name;
            });
            $datatable->editColumn('role', function ($row) {
                return $row->roles()->pluck('name')->implode(',');
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                elseif ($row->is_active == 0)
                    return '<label class="badge badge-warning">Inactive</label>';
                else
                    return '<label class="badge badge-danger">Deleted</label>';
            });

            $datatable->addColumn('action', function ($row)  {
                $actions = '';

                if(Auth::user()->can('Edit Admin Users')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/users/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-right',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));

                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active',
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);

                    $actions .= Form::close();

                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/users/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Admin Users')){

                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/users', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete User"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);

                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['role', 'name', 'status', 'action']);
            return $datatable->make(true);
        }

        return view('admin.users.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Admin Users')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }

        $data = [];
        $data['action'] = "Add";
        $data['countries'] = Countries::where('is_active', 1)->get();
        $data['roles'] = Role::get();

        if (old()) {
            $data['user'] = old();
        }

        return view('admin.users.form')->with($data);
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

        if ($input['action'] == 'Edit') {

            $user = Admin::findOrFail($input['id']);
            $this->validate($request, [
                'firstname' => 'required|string|max:20',
                'lastname' => 'required|string|max:20',
                'email' => 'required|string|email|max:100|unique:admins,email,' . $input['id'],
                'is_active' => 'required',
            ]);

            if (!empty($input['password']) && $input['password_confirmation'] <> null && $input['password'] <> null) {
                $this->validate($request, [
                    'password' => 'required|string|min:8|confirmed'
                ]);
                $input['password'] =  Hash::make($input['password']);
            } else {
                unset($input['password']);
            }

            $User = Admin::findOrFail($input['id']);

            $User->update($input);

            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 18, '', ['item_id' => $User->id]);

            // Assign Roles
            $roles = $request->get('roles');
            if (isset($roles)) {
                $User->roles()->sync($roles);
            } else {
                $User->roles()->detach();
            }

            Alert::success('Success', 'User updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'firstname' => 'required|string|max:20',
                'lastname' => 'required|string|max:20',
                'email' => 'required|string|email|max:100|unique:admins',
                'password' => 'required|string|min:8|confirmed',
                'is_active' => 'required',
            ]);

            $input['password'] = Hash::make($input['password']);

            $User = Admin::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 17, '', ['item_id' => $User->id]);
            // Assign Roles
            $roles = $request->get('roles');

            if (isset($roles)) {

                foreach ($roles as $role) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $User->assignRole($role_r);
                }
            }


            $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'create_admin')->first();
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
            Alert::success('Success', 'User added successfully.')->persistent('Close')->autoclose(5000);
        }

        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/admins/' . $User->id;
        if (!File::exists(public_path() . '/storage/uploads/admins/' . $User->id)) {

            File::makeDirectory($upload_path, 0777, true);
        }

        if (!empty($request->files) && $request->hasFile('photo')) {

            $file      = $request->file('photo');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'profile-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/admins/' . $User->id . '/' . $User->photo;

                if (file_exists($old_file) && !empty($User->photo)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/admins/') . $User->id . '/' . $file_temp_name;

                if ($type != 'svg') {
                    if ($size_mbs >= 2) {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    } else {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    }
                } else {
                    $file->move($path, $file_temp_name);
                }

                $user_image['photo'] = $file_temp_name;
                $User->update($user_image);
            }
        }


        return redirect('admin/users');
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
        }else if (!Auth::user()->can('Edit Admin Users')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['user'] = Admin::findOrFail($id);
        $data['countries'] = Countries::where('is_active', 1)->get();
        $data['action'] = "Edit";
        $data['roles'] = Role::all();
        $data['assignedRoles'] = $data['user']->roles()->pluck('id')->toArray();
        return view('admin.users.form')->with($data);
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
        }else if (!Auth::user()->can('Delete Admin Users')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        Admin::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 19, '', ['item_id' => $id]);
        Alert::success('Success', 'User deleted successfully!')->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }

    public function profile()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Profile')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['user'] = Auth::guard('admin')->user();
        $data['countries'] = Countries::where('is_active', 1)->get();
        return view('admin.users.profile', $data);
    }

    public function updateProfile(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Update Profile')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $user = Auth::guard('admin')->user();

        $old_image = $user->photo;
        $input = $request->all();

        if (!empty($input['password']) && $input['password_confirmation'] <> null && $input['password'] <> null) {
            $this->validate($request, [
                'password' => 'required|string|min:8|confirmed'
            ]);
            $input['password'] =  Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user->update($input);

        if (!empty($request->files) && $request->hasFile('photo')) {

            //MAKE DIRECTORY
            $upload_path = public_path() . '/storage/uploads/admins/' . $user->id;
            if (!File::exists(public_path() . '/storage/uploads/admins/' . $user->id)) {

                File::makeDirectory($upload_path, 0777, true);
            }

            $file      = $request->file('photo');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'profile-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/admins/' . $user->id . '/' . $user->photo;

                if (file_exists($old_file) && !empty($user->photo)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/admins/') . $user->id . '/' . $file_temp_name;

                if ($type != 'svg') {
                    if ($size_mbs >= 2) {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    } else {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    }
                } else {
                    $file->move($path, $file_temp_name);
                }

                $user_image['photo'] = $file_temp_name;
                $user->update($user_image);
            }
        }

        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 3, '', []);

        Alert::success('Success', 'Your profile information updated successfully.')->persistent('Close')->autoclose(5000);
        return redirect('admin/profile');
    }



    // Update Status
    public function update_status(Request $request)
    {
        // if (!Auth::user()->can('Update Admin')) {
        //     return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        //  }
        $input = $request->all();

        $is_active = $input['is_active'];
        $id = decode($input['id']);

        if ($is_active <> '' && $id <> '') {
            $data = array(
                'is_active' => $is_active,
            );

            Admin::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 20, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
