<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_published && $page->type === 'page', 404);

        return view('pages.show', compact('page'));
    }

    public function blogIndex()
    {
        $articles = Page::published()->where('type', 'article')->latest('published_at')->paginate(9);

        return view('pages.blog-index', compact('articles'));
    }

    public function blogShow(Page $page)
    {
        abort_unless($page->is_published && $page->type === 'article', 404);

        $related = Page::published()
            ->where('type', 'article')
            ->whereKeyNot($page->id)
            ->limit(3)
            ->get();

        return view('pages.blog-show', compact('page', 'related'));
    }
}
