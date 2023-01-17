<?php

function _asset($path, $secure = null)
{
    return asset(trim($path, '/'), $secure); //. '?var=' . config('constants.ASSET_VERSION');
}

function settingValue($key)
{
    $setting = \DB::table('site_settings')->select($key)->first();
    if ($setting)
        return $setting->$key;
    else
        return '';
}
function sitesSetting($key)
{
    $setting = \DB::table('site_settings')->selectRaw($key)->first();
    if ($setting)
        return $setting;
    else
        return '';
}
//Encode Helper
function encode($string = '')
{
    return Hashids::encode($string);
}

function decode($string = '')
{

    try {
        $id =  Hashids::decode($string);
        if ($id) {
            return $id[0];
        } else {
            abort(404);
        }
    } catch (Exception $e) {
        abort(404);
    }
}

function decodeApiIds($string = '')
{

    try {
        $id =  Hashids::decode($string);
        if ($id) {
            return $id[0];
        } else {
            return 0;
        }
    } catch (Exception $e) {
        return 0;
    }
}

function removeHtml($string)
{

    return preg_replace("/<.*?>/", "", $string);
}

function removeUrls($string)
{

    return preg_replace("/((http(s)?(\:\/\/))+(www\.)?([\w\-\.\/])*(\.[a-zA-Z]{2,3}\/?)|(www\.)?([\w\-\.\/])*(\.[a-zA-Z]{2,3}\/?))[^\s\b\n|]*[^.,;:\?\!\@\^\$ -]/", "", $string);
}

function aasort(&$array, $key, $order = 0)
{

    $sorter = array();
    $ret = array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii] = $va[$key];
    }
    if ($order == 0) {
        asort($sorter);
    } else {
        arsort($sorter);
    }
    foreach ($sorter as $ii => $va) {
        $ret[$ii] = $array[$ii];
    }
    $array = $ret;
}

function footer_menu($limit, $slugs)
{

    $where = '';
    if (!empty($slugs) && is_array($slugs)) {
        $where .= '(';
        foreach ($slugs as $slg) {
            $where .= 'LOWER(cms_pages.seo_url) LIKE "%' . strtolower($slg) . '%" OR ';
        }
        $where = rtrim($where, 'OR ');
        $where .= ')';
    }

    $result = \DB::table('cms_pages')
        ->select('*')
        ->where('show_in_footer', 1)
        ->where('is_active', 1)
        ->whereRaw($where)
        ->orderBy('sort_by', 'ASC')
        ->limit($limit)
        ->get();

    return $result;
}

function checkImage($path = '', $img = 'large', $profile = 0)
{
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    if ($extension == 'svg') {
        return $path;
    } else {

        if (@getimagesize($path)) {
            return $path;
        } else {
            if ($profile == 1) {
                return asset('frontend/dashboard/images/user-thumb-sm.png');
            } else {
                if ($img == 'large') {
                    return asset('backend/images/no_img.jpg');
                } else {
                    return asset('backend/images/no_image.jpg');
                }
            }
        }
    }
}
function getAution($value)
{


    switch ($value) {
        case $value == "12 hours":
            $auction_time = \Carbon\Carbon::now()->addHours(12);
            break;
        case $value == "24 hours":
            $auction_time = \Carbon\Carbon::now()->addHours(24);
        case $value == "2 days":
            $auction_time = \Carbon\Carbon::now()->addDays(2);
            break;
        case $value == "3 days":
            $auction_time = \Carbon\Carbon::now()->addDays(3);
            break;
        case $value == "7 days":
            $auction_time = \Carbon\Carbon::now()->addDays(7);

            break;
    }

    return $auction_time;
}


function mediaHash($media_hash)
{
    $hash = json_decode($media_hash, true);
    $file_type = explode("/", $hash["fileType"]);
    $file_type = $file_type[0];
    $media_url = config('constants.IPFS_URL') . "/" . $hash["hash"];
    return [$file_type , $media_url];
    
}


