<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Traits\ApiResponse;
use App\Traits\SaveFile;
use Illuminate\Support\Facades\File;

class homeBannerController extends Controller
{
    use ApiResponse, SaveFile;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = HomeBanner::get();
        return view('admin/HomeBanner/home_banners', get_defined_vars());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'text'    => 'required|string|max:255',
            'image'   => 'mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5 MB
            'link'    => 'required|string|max:255',
            'id'      => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'message' => $validation->errors()], 400);
        } else {
            if($request->id > 0) {
                $image = HomeBanner::find($request->id);
                $imageName = $image->image;
                $imageName = $this->saveImage($request->image, $imageName,'images/homeBanner');
            } else {
                $imageName = $this->saveImage($request->image, '','images/homeBanner');
            };

            HomeBanner::updateOrCreate(
                ['id' => $request->id],
                [
                    'text'  => $request->text,
                    'link'  => $request->link,
                    'image' => $imageName
                ]
            );
            return $this->success(['reload' => true], 'Successfully updated');
        }
    }

}

