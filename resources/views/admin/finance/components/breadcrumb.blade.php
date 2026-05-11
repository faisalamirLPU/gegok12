<nav aria-label="breadcrumb">

    <ol class="breadcrumb mb-4">

        <li class="breadcrumb-item">
            <a href="#">
                Dashboard
            </a>
        </li>

        @foreach($items as $item)

            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">

                {{ $item }}

            </li>

        @endforeach

    </ol>

</nav>
