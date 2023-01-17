<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use Auth;
class ReviewsController extends Controller
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
        }else if (!Auth::user()->can('View Reviews')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = Review::orderBy('id','DESC')->get();

            $datatable = DataTables::of($db_record);

            $datatable = $datatable->editColumn('product', function ($row) {
                return empty($row->product->title) ? '' : $row->product->title;
            });

            $datatable = $datatable->editColumn('rating', function ($row) {
                $start = '';
                for($i=0; $i<5; ++$i){
                $start .= '<i style="color:#f4c006;" class="fas fa-star'.($row->rating<=$i?'-o':'').'" aria-hidden="true"></i>';
                }
                $return = '<span>'.$start.'</span>';
                return $return;
            });
            $datatable = $datatable->editColumn('feedback', function ($row) {
                return $row->review;
            });
            $datatable = $datatable->editColumn('reviewer_user', function ($row) {
                return $row->reviewer_user->username;
            });

            $datatable = $datatable->editColumn('seller', function ($row) {
                if ($row->seller->user_type == 1){
                    return $row->seller->username.':'.'<label>Investor</label>';
                }elseif($row->seller->user_type == 2){
                    return $row->seller->username.':'.'<label>Artist</label>';

                }elseif($row->seller->user_type == 3){
                    return $row->seller->username.':'.'<label>Musician</label>';

                }

                else
                    return '<label class="badge badge-warning"></label>';
            });
            $datatable = $datatable->rawColumns(['reviewer_user','seller','product', 'rating','feedback']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.reviews.index')->with($data);
    }
}
