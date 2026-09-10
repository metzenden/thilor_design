<x-layouts.shop :title="($page->meta_title ?: $page->title).' — THILOR DESIGN'" :metaDescription="$page->meta_description ?: $page->excerpt">
    <div class="container-shop py-12">
        <div class="mx-auto max-w-3xl">
            <h1 class="section-title">{{ $page->title }}</h1>
            <div class="prose prose-sm mt-6 max-w-none text-ink/70">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</x-layouts.shop>
