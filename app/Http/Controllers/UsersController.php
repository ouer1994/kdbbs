<?php

namespace App\Http\Controllers;

use App\Handles\ImageUploadHander;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'show']);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user, ImageUploadHander $hander)
    {
        $this->authorize('update', $user);
        $data = $request->input();
        $this->validate($request, [
            'name' => 'required|between:3,25|regex:/^[a-zA-Z0-9\-\+]+$/|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
            'avatar' => 'mimes:png,jpg,jpeg,gif|dimensions:min_width=200,min_height=200'
        ], [
            'avatar.mimes' => '只允许上传 png,jpg,jpeg,gif 后缀的图片!',
            'avatar.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上'
        ]);

        if ($request->avatar) {
            $upload_path = $hander->upload_image($request->avatar, 'avatar', $user->id, 362);
            if ($upload_path) {
                $data['avatar'] = $upload_path['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '更新个人资料成功');
    }
}
