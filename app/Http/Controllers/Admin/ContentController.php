<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Content;

class ContentController extends Controller
{
    public function __construct() {
        $this->middleware(function ($request, $next) {
            abort_unless(optional(auth()->user()->role)->role == 'admin', 401);

            return $next($request);
        });
    }

    public function index() {
        $allContent = Content::all();

        return view('admin.content.index', compact('allContent'));
    }

    public function create() {
        return view('admin.content.create');
    }

    public function edit(Content $content) {
        return view('admin.content.edit', compact('content'));
    }

    public function store() {
        $content = Content::create(request()->only(['title', 'body', 'content_type_id']) + ['user_id' => auth()->user()->id]);

        return redirect("/admin/content/{$content->id}/edit");
    }

    public function update(Content $content) {
        $content->update(request()->only(['title', 'body', 'content_type_id']));

        return redirect("/admin/content/{$content->id}/edit");
    }
}
