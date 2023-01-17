<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Alert;
use Carbon\Carbon;
use Storage;
use Image;
use DB;
use Illuminate\Support\Facades\Validator;
use Auth;
class SiteSettingsController extends Controller
{

    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Setting')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['settings'] = SiteSettings::first();
        return view('admin.settings.settings', $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Update Setting')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $input = $request->all();

        unset($input['_token']);

        if (!empty($request->files) && $request->hasFile('site_logo')) {

            $file      = $request->file('site_logo');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'profile-' . time() . '.' . $type;

                $old = settingValue('site_logo');
                $old_file = public_path() . '/storage/uploads/images/'  . $old;

                if (file_exists($old_file) && !empty($old) && $old <> null) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/images');
                $file->move($path, $file_temp_name);

                $input['site_logo'] = $file_temp_name;
            }
        }
        $input['launch_time'] = Carbon::parse($input['launch_time'])->format('Y-m-d H:i:s');
        if ($input['id'] <> '') {

            $Sites = SiteSettings::findOrFail($input['id']);
            $Sites->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 4, '', []);
            Alert::success('Success Message', 'Site settings updated successfully!')->persistent('Close')->autoclose(5000);
        } else {

            SiteSettings::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 4, '', []);
            Alert::success('Success Message', 'Site settings added successfully!')->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/site-settings');
    }
}
