<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stores;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Http\Request;
use Alert;
use Image;
use Hash;
use File;
use View;
use DataTables;
use Form;
use Illuminate\Support\Facades\Validator;
use Auth;

class StoresController extends Controller
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
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('View Store')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $db_record =  Stores::where('id', '<>', 0);
            if ($request->has('user') && !empty($request->user)) {
                $db_record = $db_record->where('user_id',$request->user);
            }
            $db_record=$db_record->orderByDesc('id')->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->editColumn('username', function ($row) {

                return $row->user->username;
            });

            $datatable = $datatable->editColumn('category', function ($row) {

                return empty($row->storeCategory->title) ? '' : $row->storeCategory->title;
            });
            $datatable = $datatable->editColumn('total_products', function ($row) {


                if (count($row->products->where('price_type', 0)) > 0)

                    return '<a href="' . url("admin/products?store=" . encode($row->id))  . '"><label class="badge badge-success">' . count($row->products->where('price_type', 0)) . '</label></a>';

                else
                    return '<label class="badge badge-light">' . count($row->products->where('price_type', 0)) . '</label>';
            });


            $datatable = $datatable->editColumn('total_auction_products', function ($row) {


                if (count($row->products->where('price_type', '!=', 0)) > 0)

                    return '<a href="' . url("admin/auction-products?store=" . encode($row->id))  . '"><label class="badge badge-success">' . count($row->products->where('price_type', '!=', 0)) . '</label></a>';

                else
                    return '<label class="badge badge-light">' . count($row->products->where('price_type', '!=', 0)) . '</label>';
            });

            $datatable = $datatable->editColumn('image', function ($row) {

                return '<img src="' . checkImage(asset('storage/uploads/stores/' . $row->id . '/' . $row->image)) . '" class="image-display " id="image" style="width:  80px;border:  1px solid #ccc;" />';;
            });
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';

                // $actions .= Form::open([
                //     'method' => 'POST',
                //     'url' => ['admin/stores/update-status'],
                //     'style' => 'display:table;margin-right:10px;',
                //     'class' => 'float-sm-right',
                //     'id' => 'statusForm' . $row->id
                // ]);
                // $actions .= Form::hidden('id', encode($row->id));
                // $actions .= Form::select('is_active', [
                //     '0' => 'Inactive',
                //     '1' => 'Active',

                // ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                // $actions .= Form::close();

                //$actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/artists/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                if (count($row->products) == 0) {
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/stores', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Store"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }


                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/stores/" . encode($row->id)) . '" title="store view" target="_blank"><i class="fa fa-eye"></i></a>';
                return $actions;
            });



            $datatable = $datatable->rawColumns(['image', 'total_products', 'total_auction_products', 'status', 'action']);
            return $datatable->make(true);
        }
        $data['users'] = User::where('is_active',1)->orderBy('username','ASC')->get();
        return view('admin.stores.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Add Store')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";

        if (old()) {
            $data['store'] = old();
        }
        $data['users'] = User::where('is_active', 1)->where('user_type', '<>', 1)->get();
        $data['categories'] = Categories::where('is_active', 1)->get();
        return view('admin.stores.form')->with($data);
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
            $store = Stores::findOrFail($input['id']);



            $this->validate($request, [
                'store_title' => ['required', 'string',   'max:20'],
                'sub_title' => ['required', 'string',  'max:20'],


            ], []);


            $store = Stores::findOrFail($input['id']);

            $store->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 11, '', ['item_id' => $store->id]);
            Alert::success('Success', 'Store updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'store_title' => ['required', 'string',   'max:20'],
                'sub_title' => ['required', 'string',  'max:20'],


            ]);



            $store = Stores::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 10, '', ['item_id' => $store->id]);

            Alert::success('Success', 'Store added successfully.')->persistent('Close')->autoclose(5000);
        }
        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/stores/' . $store->id;
        if (!File::exists(public_path() . '/storage/uploads/stores/' . $store->id)) {

            File::makeDirectory($upload_path, 0777, true);
        }

        if (!empty($request->files) && $request->hasFile('image')) {

            $file      = $request->file('image');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'store-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/stores/' . $store->id . '/' . $store->image;

                if (file_exists($old_file) && !empty($store->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/stores/') . $store->id . '/' . $file_temp_name;

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

                $image['image'] = $file_temp_name;
                $store->update($image);
            }
        }
        return redirect('admin/stores');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Edit Store')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['store'] = Stores::findOrFail($id);
        $data['action'] = "Edit";
        $data['users'] = User::where('is_active', 1)->where('user_type', '<>', 1)->get();
        $data['categories'] = Categories::where('is_active', 1)->get();
        return view('admin.stores.form')->with($data);
    }


    /**
     * Store Details.
     *
     * @param  \App\Models\Stores  $stores
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decode($id);
        $data['store']  = Stores::findOrFail($id);

        return view('admin.stores.show')->with($data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Delete Store')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $store = Stores::findOrFail(decode($id));
        $filename = public_path() . '/storage/uploads/stores/' . $store->id ;
        $store = $store->delete();
        File::deleteDirectory($filename);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 21, '', ['item_id' => $id]);
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

            Stores::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 13, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
