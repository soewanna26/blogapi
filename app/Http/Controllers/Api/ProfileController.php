<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->guard()->user();
        $data = new ProfileResource($user); //singal
        // $data = ProfileResource::collection(); // multiple
        return ResponseHelper::success($data);
    }
}
