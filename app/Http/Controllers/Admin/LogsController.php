<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserEvent;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use Auth;
class LogsController extends Controller
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
        }else if (!Auth::user()->can('View Event Log')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();

        // $db_record = UserEvent::with('eventType')->orderByDesc('id')->get();
        // dd($db_record);

        if ($request->ajax()) {
            $db_record = UserEvent::with('eventType')->orderByDesc('id');

            $datatable = DataTables::of($db_record);

            $datatable = $datatable->editColumn('admin_name', function ($row) {
                return $row->meta['firstname'].' '.$row->meta['lastname'];
            });
            $datatable = $datatable->editColumn('event_type', function ($row) {
                return empty($row->eventType->event_name) ? '' : $row->eventType->event_name;
            });

            $datatable = $datatable->editColumn('created_at', function ($row) {
                return date('M d,Y', strtotime($row->created_at));
            });

            $datatable = $datatable->rawColumns(['admin_name', 'event_type','created_at']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.logs.index')->with($data);
    }
}
