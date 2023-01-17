<?php

namespace App\Http\Controllers\Admin;

use Form;
use Hashids;
use App\Models\Ads;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\ProductMediaFiles;
use App\Models\Products;
use App\Models\User;
use Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
class AdController extends Controller
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
        }else if (!Auth::user()->can('View Ads')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data =  Ads::get();
            if($request->has('user') && !empty($request->user)){
                $data = $data->where('user_id',$request->user);
            }
            $datatable = Datatables::of($data);


            $datatable = $datatable->editColumn('users', function ($row) {
                if ($row->users->user_type == 1){
                    return $row->users->username.':'.'<label>Investor</label>';
                }elseif($row->users->user_type == 2){
                    return $row->users->username.':'.'<label>Artist</label>';

                }elseif($row->users->user_type == 3){
                    return $row->users->username.':'.'<label>Musician</label>';

                }

                else
                    return '<label class="badge badge-warning"></label>';
            });
            $datatable = $datatable->editColumn('start_date', function ($row) {
                return Carbon\Carbon::parse(( $row->start_date));
            });
            $datatable = $datatable->editColumn('end_date', function ($row) {
                return Carbon\Carbon::parse(( $row->end_date));
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<i class="badge badge-primary"></i>';
                if ($row->is_active == 1) {
                    $status = ' <label class="badge badge-success">Active</label>';
                } else if ($row->is_active == 0) {
                    $status = '<label class="badge badge-warning">Inactive</label>';
                }

                return $status;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Ad')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/ads/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-left',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active'
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                     $actions .= Form::close();

                     $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/ads/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';
                }
                if(Auth::user()->can('Delete Ad')){
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/ads', encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete ads"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);

                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
                $actions .= '';
                return $actions;
            });



            $datatable = $datatable->rawColumns(['users','start_date','end_date','status','action']);

            return $datatable->make(true);
        }
        $data['users'] = User::where('is_active',1)->orderBy('username','ASC')->get();

        return view('admin.ads.index')->with($data);
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Ads')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data = [];
        $data['ads']  = Ads::where('id', $id)->first();

        return view('admin.ads.show')->with($data);
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Ad')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        Ads::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 38, '', ['item_id' => $id]);
        Alert::success('Success', 'Ad deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Ads::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 39, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
