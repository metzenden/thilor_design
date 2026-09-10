<x-layouts.shop title="Blog — THILOR DESIGN">
    <div class="container-shop py-12">
        <h1 class="section-title mb-8">Blog</h1>
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                <a href="{{ route('blog.show', $article) }}" class="block">
                    @if($article->cover_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="{{ $article->title }}" class="aspect-video w-full rounded-sm object-cover">
                    @endif
                    <p class="mt-3 text-xs text-ink/40">{{ $article->published_at?->format('d/m/Y') }}</p>
                    <h2 class="mt-1 font-serif text-lg font-semibold text-ink">{{ $article->title }}</h2>
                    <p class="mt-1 text-sm text-ink/60">{{ $article->excerpt }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $articles->links() }}</div>
    </div>
</x-layouts.shop>
