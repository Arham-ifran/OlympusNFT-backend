<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
class Languages extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'flag',
        'is_active'
    ];
    protected $appends = ['lang_flag'];

    public function getLangFlagAttribute()
    {

        if ($this->flag != "") {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/flag/' . $this->id . '/' . $this->flag)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/flag/' . $this->id . '/' . $this->flag);
            } else {
                $image = asset('backend/images/tranlate.png');
            }
        } else {
            $image = asset('backend/images/tranlate.png');
        }
        return $this->attributes['lang_flag'] = $image;
    }
}
