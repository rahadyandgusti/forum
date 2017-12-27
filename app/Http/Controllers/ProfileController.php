<?php

namespace App\Http\Controllers;

use Auth;
use DevDojo\Chatter\Models\Models;
use Illuminate\Routing\Controller as Controller;

class ProfileController extends Controller
{
    public function index($username, $slug = '')
    {
        // $id = \App\User::where('username',$username)->first();
        $id = $username;

        $userData = \App\User::where('id',$username)->first();

        $pagination_results = config('chatter.paginate.num_of_results');

        $discussions = Models::discussion()->with('user')->with('post')->with('postsCount')->with('category')->orderBy('created_at', 'DESC')->where('user_id',$id)->paginate($pagination_results);
        if (isset($slug)) {
            $category = Models::category()->where('slug', '=', $slug)->first();
            if (isset($category->id)) {
                $discussions = Models::discussion()->with('user')->with('post')->with('postsCount')->with('category')->where('chatter_category_id', '=', $category->id)->where('user_id',$id)->orderBy('created_at', 'DESC')->paginate($pagination_results);
            }
        }

        $categories = Models::category()->all();
        $chatter_editor = config('chatter.editor');

        if ($chatter_editor == 'simplemde') {
            // Dynamically register markdown service provider
            \App::register('GrahamCampbell\Markdown\MarkdownServiceProvider');
        }

        return view('profile', compact('discussions', 'categories', 'chatter_editor', 'userData'));
    }
}
