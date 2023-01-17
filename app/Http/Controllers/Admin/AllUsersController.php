<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Form;
use DataTables;
use Alert;
class AllUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        if($request->ajax()){
            $data =  User::get();
            $datatable = DataTables::of($data);
            $datatable = $datatable->editColumn('status',function($row){
                 if($row->is_active == 1){
                     return '<label class="badge badge-success">Active</label>';

                 }else{
                     return '<label class="badge badge-warning">InActive</label>';

                 }
            });

            // $datatable = $datatable->editColumn('product',function($row){
            //      return $row->product->title;
            // });
            $datatable = $datatable->editColumn('user_type',function($row){
             if ($row->user_type == 1){
                 return $row->username.':'.'<label>Investor</label>';
             }elseif($row->user_type == 2){
                 return $row->username.':'.'<label>Artist</label>';

             }elseif($row->user_type == 3){
                 return $row->username.':'.'<label>Musician</label>';

             }

             else
                 return '<label class="badge badge-warning"></label>';
            });

            $datatable->addColumn('action',function($row){
                 $actions = '';

                 $actions .= Form::open([
                     'method'=>'POST',
                     'url'=> ['admin/all-users/update-status'],
                     'style' => 'display:table;margin-right:10px;',
                     'class' => 'float-sm-right',
                     'id' => 'statusForm' . $row->id
                 ]);
                 $actions .= Form::hidden('id',encode($row->id));
                 $actions .= Form::select('is_active',[
                     '0' => 'Inactive',
                     '1' => 'Active'
                 ], $row->is_active,['class' => 'form-control', 'onchange'=>'$(form).submit();']);
                 $actions .= Form::close();

                 $actions .= '&nbsp;<a class="btn btn-primary btn-icon"' . url("admin/all-users/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';

                 $actions .= '&nbsp;' . Form::open([
                     'method' => 'DELETE',
                     'url' => ['admin/all-users', encode($row->id)],
                     'style' => 'display:inline'
                 ]);

                 $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete History"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                 $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                 $actions .= Form::close();
                 return $actions;

            });

            $datatable = $datatable->rawColumns(['status','user_type','action']);
            return $datatable->make(true);

         }
         return view('admin.all-users.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function update_status(Request $request)
    {
        $input = $request->all();
        $is_active = $input['is_active'];
        $id = decode($input['id']);
        if($is_active <> '' && $id <> ''){
            $data = array(
                'is_active' => $is_active,
            );
            User::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin',13,'',['item_id'=>$id]);
            Alert::success('Success','status update successfully!')->persistent('Close')->autoclose(5000);
        }else{
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);

        }
        return redirect()->back();
    }
}
