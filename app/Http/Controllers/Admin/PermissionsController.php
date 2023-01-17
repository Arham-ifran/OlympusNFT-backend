<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables;
use Form;
use Alert;
use Auth;
class PermissionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Permission')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        if ($request->ajax()) {
            $data = Permission::all();
            $datatable = Datatables::of($data);
            $datatable->editColumn('permission', function ($row) {
                $permission = $row->name;
                return $permission;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Permission')){
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/permissions/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Permission')){
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/permissions', encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Role"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                $actions .= Form::close();
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['permission', 'action']);
            return $datatable->make(true);
        }
        return view('admin.permissions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Permission')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['roles'] = Role::get();
        $data['action'] = 'Add';
        return view('admin.permissions.form')->with($data);
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

            // if (!Auth::user()->can('Update Permission')) {
            //     return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
            // }
            $id = $input['id'];
            $permission = Permission::findOrFail($id);

            $this->validate($request, [
                'name' => 'required|max:255',
            ]);

            $input = $request->all();

            $roles = $input['roles'] ?? null;
            $permission->fill($input)->save();
            $p_all = Role::all();

            foreach ($p_all as $p) {
                $permission->removeRole($p);
            }
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $p = Role::where('id', '=', $role)->firstOrFail(); //Get corresponding form roles in db
                    $permission->assignRole($p);
                }
            }
            \App\Events\UserEvents::dispatch('admin', 14, '', ['item_id' => $id]);
            Alert::success('Success', 'Permission updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'name' => 'required|max:255',
                'guard_name' => 'required|max:255',
            ]);

            $name = $request->get('name');
            $permission = new Permission();

            $permission->name = $name;
            $roles = $request->get('roles');
            $permission->save();

            if (!empty($request->get('roles'))) {
                foreach ($roles as $role) {
                    $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                    $permission = Permission::where('name', '=', $name)->first();
                    $r->givePermissionTo($permission);
                }
            }
            \App\Events\UserEvents::dispatch('admin', 13, '', ['item_id' => $id]);
            Alert::success('Success', 'Permission added successfully!')->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/permissions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Permission')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['roles'] = Role::all();
        $data['permission'] = Permission::find(decode($id));
        $data['assignedRoles'] = $data['permission']->roles()->pluck('id')->toArray();
        $data['action'] = 'Edit';
        return view('admin.permissions.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Permission')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $permission = Permission::findOrFail(decode($id));
        if ($permission->name == "Super Admin") {

            Alert::warning('Warning', 'This Permission cannot be deleted.')->persistent('Close')->autoclose(5000);
            return redirect('admin/permissions');
        }

        $permission->delete();
        \App\Events\UserEvents::dispatch('admin', 15, '', ['item_id' => $id]);
        Alert::success('Success', 'Permission deleted Successfully.')->persistent('Close')->autoclose(5000);
        return redirect('admin/permissions');
    }
}
