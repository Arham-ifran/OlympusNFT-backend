<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategories;
use App\Models\Faqs;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use Auth;
class FaqsController extends Controller
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
        }else if (!Auth::user()->can('View Faq')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = Faqs::orderByDesc('id');

            $datatable = DataTables::of($db_record);


            $datatable->editColumn('category', function ($row) {
                return $row->faqCategory->title;
            });
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                return '<label class="badge badge-success">Active</label>';
                else
                return '<label class="badge badge-warning">Inactive</label>';
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Faq')){
                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/faqs/update-status'],
                    'style' => 'display:table;margin-right:10px;',
                    'class' => 'float-sm-left',
                    'id' => 'statusForm' . $row->id
                ]);
                $actions .= Form::hidden('id', encode($row->id));
                $actions .= Form::select('is_active', [
                    '0' => 'Inactive',
                    '1' => 'Active'
                ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/faqs/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                $actions .= Form::close();
                }

                if(Auth::user()->can('Delete Faq')){
                $actions .= Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/faqs', encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Faq"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::close();
                }

                $actions .= '';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['category', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.faqs.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Faq')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data['action'] = "Add";
        if (old()) {
            $data['faq'] = old();
        }
        $data['faqCategories'] = FaqCategories::where('is_active', 1)->get();
        return view('admin.faqs.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Faq')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['action'] = "Edit";
        $data['faqCategories'] = FaqCategories::where('is_active', 1)->get();
        $data['faq'] = Faqs::findOrFail($id);
        return view('admin.faqs.form')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if ($input['action'] == 'Edit') {
            $Faqs = Faqs::findOrFail($input['id']);
            $this->validate($request, [
                'category_id' => 'required',
                'title' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'required',
            ]);


            $Faqs->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 49, '', ['item_id' => $Faqs->id]);
            Alert::success('Success', 'Faq updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'category_id' => 'required',
                'title' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'required',
            ]);

            $Faqs = Faqs::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 48, '', ['item_id' => $Faqs->id]);
            Alert::success('Success', 'Faq added successfully!')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/faqs');
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
        }else if (!Auth::user()->can('Delete Faq')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        Faqs::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 50, '', ['item_id' => $id]);
        Alert::success('Success', 'Faq deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Faqs::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 51, '', ['item_id' => $id]);
            Alert::success('Success', 'Faq status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. CMS Page status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
