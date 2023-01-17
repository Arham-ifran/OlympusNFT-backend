<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables;
use Form;
use Alert;
use Auth;
class RolesController extends Controller
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
        }else if (!Auth::user()->can('View Role')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        if ($request->ajax()) {
            $data = Role::all();
            $datatable = Datatables::of($data);
            $datatable->editColumn('role', function ($row) {
                $role = $row->name;
                return $role;
            });
            $datatable->addColumn('permissions', function ($row) {
                $permission = $row->permissions()->pluck('name')->implode(', ');
                return $permission;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Role')){
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/roles/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Role')){
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/roles', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Role"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['role', 'permissions', 'action']);
            return $datatable->make(true);
        }
        return view('admin.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Role')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['permissions'] = Permission::all();
        $data['action'] = 'Add';
        return view('admin.roles.form')->with($data);
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

            $id = $input['id'];
            $role = Role::findOrFail($id);
            $this->validate($request, [
                'name' => 'required|max:255|unique:roles,name,' . $id,
                'permissions' => 'required',
            ]);

            $input = $request->except(['permissions']);

            $permissions = $request->get('permissions');
            $role->fill($input)->save();
            $p_all = Permission::all();

            foreach ($p_all as $p) {
                $role->revokePermissionTo($p);
            }

            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form permission in db
                $role->givePermissionTo($p);
            }
            \App\Events\UserEvents::dispatch('admin', 10, '', ['item_id' => $id]);
            Alert::success('Success', 'Role updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            $this->validate(
                $request,
                [
                    'name' => 'required|unique:roles|max:255',
                    'guard_name' => 'required|max:255',
                    'permissions' => 'required',
                ]
            );

            $role = new Role();
            $role->name = $input['name'];
            $permissions = $input['permissions'];
            $role->save();

            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role = Role::where('name', '=', $input['name'])->first();
                $role->givePermissionTo($p);
            }
            \App\Events\UserEvents::dispatch('admin', 9, '', ['item_id' => $id]);
            Alert::success('Success', 'Role added successfully!')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/roles');
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
        }else if (!Auth::user()->can('Edit Role')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['role'] = Role::findOrFail(decode($id));
        $data['assignedPermission'] = $data['role']->permissions()->pluck('id')->toArray();
        $data['permissions'] = Permission::all();
        $data['action'] = 'Edit';
        return view('admin.roles.form')->with($data);
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
        }else if (!Auth::user()->can('Delete Role')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $role = Role::findOrFail(decode($id));
        $role->delete();
        \App\Events\UserEvents::dispatch('admin', 11, '', ['item_id' => $id]);
        Alert::success('Success', 'Role deleted Successfully.')->persistent('Close')->autoclose(5000);
        return redirect('admin/roles');
    }
}
