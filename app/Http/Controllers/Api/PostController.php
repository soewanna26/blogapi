<?php

namespace App\Http\Controllers\APi;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('category','user','image')
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
    public function create(Request $request)
    {
        $request->validate(
            [
                'title' => 'required',
                'description' => 'required',
                'category_id' => 'required',
            ],
            [
                'category_id.required' => 'The Category field is required',
            ]
        );
        DB::beginTransaction();

        try {
            $file_name = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Generate file name
                $file_name = uniqid() . '_' . date('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();

                // Debugging: Log file path
                // Log::info('File path: ' . storage_path('app/media/' . $file_name));

                // Save file to storage
                Storage::put('media/' . $file_name, file_get_contents($file));

                // Debugging: Check if file saved
                if (!Storage::exists('media/' . $file_name)) {
                    throw new Exception('File not saved to storage.');
                }
            }

            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->title = $request->title;
            $post->description = $request->description;
            $post->category_id = $request->category_id;
            $post->save();

            $media = new Media();
            $media->file_name = $file_name;
            $media->file_type = 'image';
            $media->model_id = $post->id;
            $media->model_type = Post::class;
            $media->save();

            DB::commit();
            return ResponseHelper::success([], "Post created successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHelper::fail($e->getMessage());
        }
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return ResponseHelper::success(new PostDetailResource($post));
    }
}
