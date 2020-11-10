<div class="flex">
@foreach($images as $image)
    <!-- Column -->
        <div class="my-1 px-1 w-full md:w-1/2 lg:my-4 lg:px-4 lg:w-1/3">
            <article class="overflow-hidden rounded-lg shadow-lg">
                <a href="{{ $image['path'] }}" target="_blank">
                    <img alt="Placeholder" class="block h-auto w-full" src="{{ $image['path'] }}">
                </a>
                <header class="flex items-center justify-between leading-tight p-2 md:p-4">
                    <p class="text-grey-darker text-sm">
                        {{ $image['name'] }}
                    </p>
                </header>
            </article>
        </div>
    @endforeach
</div>
