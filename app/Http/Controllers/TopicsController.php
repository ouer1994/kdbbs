<?php

namespace App\Http\Controllers;

use App\Handles\ImageUploadHander;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request, Topic $topic)
    {
        $topics = $topic->withOrder($request->order)->paginate();
        return view('topics.index', compact('topics'));
    }

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

    public function create(Topic $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

        return redirect()->route('topics.show', $topic->id)->with('success', 'Created successfully.');
    }

    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);
        return view('topics.create_and_edit', compact('topic'));
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
    }

    public function uploadImage(Request $request, ImageUploadHander $hander)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => ''
        ];

        if ($file = $request->upload_file) {
            $res_path = $hander->upload_image($file, 'topics', \Auth::id(), 1024);
            if ($res_path) {
                $data['success'] = true;
                $data['msg'] = '上传成功';
                $data['file_path'] = $res_path['path'];
            }
        }

        return $data;
    }
}