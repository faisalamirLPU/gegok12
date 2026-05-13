<div class="flex flex-wrap lg:flex-row justify-between my-3">
    <div>
        <h1 class="admin-h1 my-3">
            {{ $title ?? 'Finance Module' }}
        </h1>
        @if(isset($subtitle))
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider -mt-2 mb-2">
                {{ $subtitle }}
            </p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {!! $actions ?? '' !!}
    </div>
</div>
