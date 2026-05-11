<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            {{ $title ?? 'Finance Module' }}
        </h2>

        @if(isset($subtitle))

            <p class="text-muted mb-0">
                {{ $subtitle }}
            </p>

        @endif

    </div>

    <div>

        {!! $actions ?? '' !!}

    </div>

</div>
