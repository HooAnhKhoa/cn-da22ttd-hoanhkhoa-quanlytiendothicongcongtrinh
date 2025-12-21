<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function deletePost(Post $post) {
        if (auth()->user()->id === $post['user_id']) {
            $post->delete();
        }

        return redirect('/');
    }

    public function updatePost(Post $post, Request $request) {
        if (auth()->user()->id !== $post['user_id']) {
            return redirect('/');
        }

        $a = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $a['title'] = strip_tags($a['title']);
        $a['body'] = strip_tags($a['body']);

        $post->update($a);

        return redirect('/');
    }

    public function showEditScreen(Post $post) {
        if (auth()->user()->id !== $post['user_id']) {
            return redirect('/');
        }

        return view('edit-post', ['post' => $post]);
    }

    public function createPost(Request $request) {
        $a = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $a['title'] = strip_tags($a['title']);
        $a['body'] = strip_tags($a['body']);
        $a['user_id'] = auth()->id();
        Post::create($a);

        return redirect('/');
    }
}
