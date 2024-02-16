<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;
use App\Models\Post;
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
    public function profilePosts(Request $request)
    {
        $posts = Post::with('category','user','image')->where('user_id',auth()->user()->id)
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at')
            ->paginate(10);

        return PostResource::collection($posts)->additional(['message' => 'success']);
    }
}
