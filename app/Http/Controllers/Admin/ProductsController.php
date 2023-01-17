<?php

namespace App\Http\Controllers\Admin;

use Auth;
use File;
use Form;
use Hash;
use View;
use Alert;
use Image;
use DataTables;
use App\Models\User;
use App\Models\Stores;
use App\Models\Products;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Models\AuctionLength;
use App\Models\ProductMediaFiles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Exists;

class ProductsController extends Controller
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
        }else if (!Auth::user()->can('View Product')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $store_id = '';

        if ($request->has('store') && !empty($request->store)) {
            $store = decode($request->store);
            $store_id = ($request->store);
        }

        $data['store'] = $store_id;

        if ($request->ajax()) {
            $db_record =  Products::where('price_type', 0);

            if ($request->has('store_id') && !empty($request->store_id)  || ($request->store_id) === '0') {

                $db_record = $db_record->where('store_id',$request->store_id);

            }
            if ($request->has('user_id') && !empty($request->user_id)) {
                $db_record = $db_record->where('user_id',$request->user_id);
            }

            $db_record = $db_record->orderByDesc('id')->get();
            $datatable = Datatables::of($db_record);

            $datatable = $datatable->editColumn('user', function ($row) {
                return empty($row->user->username) ? '' : $row->user->username;
            });
            $datatable = $datatable->editColumn('store', function ($row) {
                return empty($row->store->store_title) ? 'OlympusNFT' : $row->store->store_title;
            });

            $datatable = $datatable->editColumn('category', function ($row) {
                return empty($row->category->title) ?  '' : $row->category->title;
            });
            $datatable = $datatable->editColumn('title', function ($row) {
                return empty( $row->title) ? '' : $row->title;
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';
            });

            $datatable = $datatable->editColumn('price_usd', function ($row) {
                return  empty('$' . $row->price_usd) ? '' :  '$' . $row->price_usd;
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';

                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/products/update-status'],
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
                if(Auth::user()->can('Edit Product')){
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/products/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';
                }
                if(Auth::user()->can('Delete Product')){
                    if ($row->is_sold == 0) {
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/products', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Product"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);

                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                    }
                }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['user','price_usd', 'store', 'category', 'title','status', 'action']);

            return $datatable->make(true);
        }
        $data['users'] = User::where('is_active',1)->orderBy('username','ASC')->get();
        $data['stores'] = Stores::where('is_active',1)->orderBy('store_title','ASC')->get();

        return view('admin.products.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Product')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";

        if (old()) {
            $data['product'] = old();
        }
        $data['stores'] =    Stores::where('is_active', 1)->get();
        $data['categories'] = Categories::where('is_active', 1)->get();
        $data['auction_lengths'] = AuctionLength::where('is_active', 1)->get();
        return view('admin.products.form')->with($data);
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
            $product = Products::findOrFail($input['id']);



            $this->validate($request, [
                'store_id' => ['required'],
                'category_id' => ['required'],
                'title' => ['required', 'string',   'max:100'],
                'sub_title' => ['required', 'string',  'max:100'],

            ], []);


            $product = Products::findOrFail($input['id']);

            $product->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 11, '', ['item_id' => $product->id]);
            Alert::success('Success', 'Product updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'store_id' => ['required'],
                'category_id' => ['required'],
                'title' => ['required', 'string',   'max:100'],
                'sub_title' => ['required', 'string',  'max:100'],


            ]);



            $product = Products::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 10, '', ['item_id' => $product->id]);

            Alert::success('Success', 'Product added successfully.')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/products');
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
        }else if (!Auth::user()->can('Edit Product')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['product'] = Products::findOrFail($id);
        $data['action'] = "Edit";
        $data['categories'] = Categories::where('is_active', 1)->get();
        return view('admin.products.form')->with($data);
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $id = decode($id);
        $data['product']  = Products::where('id', $id)->first();

        return view('admin.products.show')->with($data);
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
        }else if (!Auth::user()->can('Delete Product')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        $data = array(
            'is_active' => 2,
        );

        Products::whereId($id)->delete($data);

        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 31, '', ['item_id' => $id]);
        Alert::success('Success', 'Product deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Products::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 72, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
