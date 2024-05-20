<div class="fi-ta-image px-3 py-4">
    <div class="flex items-center gap-x-2.5">
        <div class="flex -space-x-2">
            @if($getState() !== null)
                @foreach($getState() as $author)
                    <img x-data x-tooltip.raw="{{ $author->name }}" src="
                                @if(!$author->image_url)
                                    {{ $getDefaultAvatar() }}
                                @elseif(!empty(parse_url($author->image_url)['scheme']))
                                    {{ $author->image_url }}
                                @elseif(empty(parse_url($author->image_url)['scheme']))
                                    {{ 'https://eng.unhas.ac.id/siminformatika' . $author->image_url }}
                                @else
                                    {{ $getDefaultAvatar() }}
                                @endif
                                " style="height: 2.5rem; width: 2.5rem;"
                        class="max-w-none object-cover object-center rounded-full bg-white ring-white dark:ring-gray-900 ring-2">
                @endforeach
            @endif
        </div>
    </div>
</div>