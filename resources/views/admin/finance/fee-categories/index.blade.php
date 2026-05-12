@extends('layouts.admin.layout')

@section('content')

<div class="relative">

    {{-- Header --}}
    <div class="flex flex-row justify-between">

        <div>

            <h1 class="admin-h1 my-3">
                Fee Categories
            </h1>

        </div>

        <div class="relative">

            <div class="flex items-center">

                <a href="{{ route('finance.fee-categories.create') }}"
                   class="no-underline text-white px-4 my-3 mx-1 flex items-center custom-green py-1 justify-center">

                    <span class="mx-1 text-sm font-semibold">
                        Add Category
                    </span>

                    <svg version="1.1"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 409.6 409.6"
                         class="w-3 h-3 fill-current text-white">

                        <g>
                            <path d="M392.533,187.733H221.867V17.067C221.867,7.641,214.226,0,204.8,0
                            s-17.067,7.641-17.067,17.067v170.667H17.067
                            C7.641,187.733,0,195.374,0,204.8
                            s7.641,17.067,17.067,17.067h170.667v170.667
                            c0,9.426,7.641,17.067,17.067,17.067
                            s17.067-7.641,17.067-17.067V221.867h170.667
                            c9.426,0,17.067-7.641,17.067-17.067
                            S401.959,187.733,392.533,187.733z">
                            </path>
                        </g>

                    </svg>

                </a>

            </div>

        </div>

    </div>


    {{-- Search --}}
    <div class="mb-4">

        <input type="text"
               id="searchInput"
               placeholder="Search Fee Category..."
               class="border px-4 py-2 bg-white w-80 focus:outline-none">

    </div>


    {{-- Table --}}
    <div class="flex flex-row justify-between custom-table overflow-x-auto tableFixHead"
         style="max-height:500px;">

        <table class="w-full">

            <thead class="bg-grey-light">

                <tr class="border-t-2 border-b-2">

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Category
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Code
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Frequency
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Refundable
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Optional
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker text-center">
                        Status
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Actions
                    </th>

                </tr>

            </thead>

            @if(count($feeCategories) != 0)

                <tbody class="bg-grey-light"
                       id="feeCategoryTable">

                    @foreach($feeCategories as $category)

                        <tr class="border-t-2 border-b-2 fee-row">

                            {{-- Name --}}
                            <td class="py-3 px-2 category-name">

                                {{ $category->name }}

                            </td>

                            {{-- Code --}}
                            <td class="py-3 px-2">

                                {{ $category->code }}

                            </td>

                            {{-- Frequency --}}
                            <td class="py-3 px-2">

                                {{ ucfirst(str_replace('_', ' ', $category->frequency)) }}

                            </td>

                            {{-- Refundable --}}
                            <td class="py-3 px-2">

                                @if($category->is_refundable)

                                    <span class="text-green-600 font-semibold">
                                        YES
                                    </span>

                                @else

                                    <span class="text-red-600 font-semibold">
                                        NO
                                    </span>

                                @endif

                            </td>

                            {{-- Optional --}}
                            <td class="py-3 px-2">

                                @if($category->is_optional)

                                    OPTIONAL

                                @else

                                    MANDATORY

                                @endif

                            </td>

                            {{-- Status --}}
                            {{-- Status --}}
<td class="py-3 px-2">

    @if($category->status)

        <div class="flex justify-center">

            <svg class="w-5 h-5 fill-current text-green-600"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 512 512">

                <path d="M256 0C114.615 0 0 114.615 0 256s114.615 256 256 256 256-114.615 256-256S397.385 0 256 0zm129.75 201.75L233.25 354.25c-4.5 4.5-10.5 6.75-16.5 6.75s-12-2.25-16.5-6.75l-74-74c-9-9-9-24 0-33s24-9 33 0l57.5 57.5L352.75 168.75c9-9 24-9 33 0s9 24 0 33z"/>

            </svg>

        </div>

    @else

        <div class="flex justify-center">

            <svg class="w-5 h-5 fill-current text-red-600"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 512 512">

                <path d="M256 0C114.615 0 0 114.615 0 256s114.615 256 256 256 256-114.615 256-256S397.385 0 256 0zm91.5 310.5c9 9 9 24 0 33s-24 9-33 0L256 285l-58.5 58.5c-9 9-24 9-33 0s-9-24 0-33L223 252l-58.5-58.5c-9-9-9-24 0-33s24-9 33 0L256 219l58.5-58.5c9-9 24-9 33 0s9 24 0 33L289 252l58.5 58.5z"/>

            </svg>

        </div>

    @endif

</td>


{{-- Actions --}}
<td class="py-3 px-2">

    <div class="flex items-center">

        {{-- Edit --}}
        <a href="{{ route('finance.fee-categories.edit', $category->id) }}"
           class="mx-1"
           title="Edit">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4 fill-current text-black"
                 viewBox="0 0 512 512">

                <path d="M290.74 93.24l128 128L142.68 497.31 0 512l14.69-142.68L290.74 93.24zM497.94 74.17l-60.11-60.11c-18.75-18.75-49.14-18.75-67.88 0l-56.56 56.56 128 128 56.56-56.56c18.75-18.74 18.75-49.13-.01-67.88z"/>

            </svg>

        </a>


        {{-- View --}}
        <a href="#"
           class="mx-1"
           title="View">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4 fill-current text-black"
                 viewBox="0 0 576 512">

                <path d="M572.52 241.4C518.29 135.59 407.4 64 288 64S57.71 135.59 3.48 241.4a48.35 48.35 0 0 0 0 29.2C57.71 376.41 168.6 448 288 448s230.29-71.59 284.52-177.4a48.35 48.35 0 0 0 0-29.2zM288 400c-97 0-189.09-57.89-238.27-144C98.91 169.89 191 112 288 112s189.09 57.89 238.27 144C477.09 342.11 385 400 288 400zm0-240a96 96 0 1 0 96 96 96 96 0 0 0-96-96z"/>

            </svg>

        </a>


        {{-- Delete --}}
        <form action="{{ route('finance.fee-categories.destroy', $category->id) }}"
              method="POST">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="mx-1"
                    onclick="return confirm('Delete this category?')">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 fill-current text-black"
                     viewBox="0 0 448 512">

                    <path d="M135.2 17.7C140.6 7.4 151.3 0 163.2 0H284.8c11.9 0 22.6 7.4 28 17.7L328 32H432c8.8 0 16 7.2 16 16V80c0 8.8-7.2 16-16 16H16C7.2 96 0 88.8 0 80V48C0 39.2 7.2 32 16 32H120l15.2-14.3zM53.2 467c1.7 25.4 22.8 45 48.2 45H346.6c25.4 0 46.5-19.6 48.2-45L416 128H32l21.2 339z"/>

                </svg>

            </button>

        </form>

    </div>

</td>

                        </tr>

                    @endforeach

                </tbody>

            @else

                <tbody class="bg-grey-light">

                    <tr class="border-t-2 border-b-2">

                        <td colspan="7"
                            class="py-3 px-2 text-center">

                            No Records Found

                        </td>

                    </tr>

                </tbody>

            @endif

        </table>

    </div>

</div>


{{-- Dynamic Search --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchInput');

    const rows =
        document.querySelectorAll('.fee-row');

    searchInput.addEventListener('keyup', function () {

        const value =
            this.value.toLowerCase();

        rows.forEach(row => {

            const name =
                row.querySelector('.category-name')
                   .innerText
                   .toLowerCase();

            row.style.display =
                name.includes(value)
                    ? ''
                    : 'none';

        });

    });

});

</script>

@endsection
