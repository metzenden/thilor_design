<x-layouts.shop :title="($page->meta_title ?: $page->title).' — THILOR DESIGN'" :metaDescription="$page->meta_description ?: $page->excerpt">
    <div class="container-shop py-12">
        <div class="mx-auto max-w-3xl">
            <nav class="mb-4 text-xs text-ink/50">
                <a href="{{ route('blog.index') }}" class="hover:text-gold">Blog</a>
                <span class="mx-1">/</span>
                <span class="text-ink">{{ $page->title }}</span>
            </nav>
            <h1 class="section-title">{{ $page->title }}</h1>
            <p class="mt-2 text-xs text-ink/40">{{ $page->published_at?->format('d/m/Y') }}</p>

            @if($page->cover_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->cover_image) }}" alt="{{ $page->title }}" class="mt-6 aspect-video w-full rounded-sm object-cover">
            @endif

            <div class="prose prose-sm mt-6 max-w-none text-ink/70">
                {!! $page->content !!}
            </div>
        </div>

        @if($related->isNotEmpty())
            <div class="mx-auto mt-12 max-w-3xl">
                <h2 class="mb-4 font-serif text-lg font-semibold">À lire aussi</h2>
                <ul class="space-y-2 text-sm">
                    @foreach($related as $item)
                        <li><a href="{{ route('blog.show', $item) }}" class="text-gold hover:underline">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-layouts.shop>
