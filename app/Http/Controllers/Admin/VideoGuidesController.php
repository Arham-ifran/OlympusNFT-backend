<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoGuides;
use Illuminate\Http\Request;
use Alert;
use App\Rules\YoutubeUrl;
use Image;
use Hash;
use File;
use View;
use DataTables;
use Form;
use Illuminate\Support\Facades\Validator;
use Auth;
class VideoGuidesController extends Controller
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
        }else if (!Auth::user()->can('View Video Guides')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data = VideoGuides::get();
            $datatable = Datatables::of($data);
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';
            });


            $datatable->addColumn('action', function ($row) {
                $actions = '';

                if(Auth::user()->can('Edit Video Guides')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/video-guides/update-status'],
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

                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/video-guides/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Video Guides')){
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/video-guides', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Category"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }
                return $actions;
            });



            $datatable = $datatable->rawColumns(['status', 'action']);
            return $datatable->make(true);
        }

        return view('admin.videoGuides.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Video Guides')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";


        if (old()) {
            $data['videoGuide'] = old();
        }

        return view('admin.videoGuides.form')->with($data);
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

            $this->validate($request, [
                'title' => ['required', 'string', 'max:500'],
                'url' => ['required', 'url'],

            ]);

            $videoGuide = VideoGuides::findOrFail($input['id']);

            $videoGuide->update($input);

            \App\Events\UserEvents::dispatch('admin', 65, '', ['item_id' => $videoGuide->id]);
            Alert::success('Success', 'Video Guide updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'title' => ['required', 'string', 'max:500'],
                'url' => ['required', 'url']

            ]);


            $videoGuide = VideoGuides::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 64, '', ['item_id' => $videoGuide->id]);

            Alert::success('Success', 'Video Guide added successfully.')->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/video-guides');
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
        }else if (!Auth::user()->can('Edit Video Guides')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['videoGuide'] = VideoGuides::findOrFail($id);
        $data['action'] = "Edit";
        return view('admin.videoGuides.form')->with($data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function destroy($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Video Guides')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        VideoGuides::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 66, '', ['item_id' => $id]);
        Alert::success('Success', 'Video Guide deleted successfully!')->persistent('Close')->autoclose(5000);
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

            VideoGuides::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 67, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
