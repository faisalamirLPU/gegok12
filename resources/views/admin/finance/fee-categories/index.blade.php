@extends('layouts.admin.layout')

@section('title', 'Fee Categories')

@section('content')

<div class="container-fluid">

    @include('admin.finance.partials.alerts')

    @include('admin.finance.components.breadcrumb', [
        'items' => ['Finance', 'Fee Categories']
    ])

    @include('admin.finance.components.header', [
        'title' => 'Fee Categories',
        'subtitle' => 'Manage ERP finance categories',
        'actions' => '
            <a href="'.route('finance.fee-categories.create').'"
               class="btn btn-primary">

                <i class="fas fa-plus-circle me-1"></i>

                Add Category

            </a>
        '
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="row mb-4">

                <div class="col-md-4">

                    <form method="GET">

                        <div class="input-group">

                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search fee category...">

                            <button class="btn btn-outline-secondary">

                                <i class="fas fa-search"></i>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="70">#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Frequency</th>
                            <th>Refundable</th>
                            <th>Optional</th>
                            <th>Status</th>
                            <th width="180">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($feeCategories as $category)

                            <tr>

                                <td>

                                    {{ $loop->iteration }}

                                </td>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $category->name }}

                                    </div>

                                </td>

                                <td>

                                    <span class="badge bg-dark">

                                        {{ $category->code }}

                                    </span>

                                </td>

                                <td>

                                    <span class="badge bg-info text-dark">

                                        {{ ucfirst(str_replace('_', ' ', $category->frequency)) }}

                                    </span>

                                </td>

                                <td>

                                    @if($category->is_refundable)

                                        <span class="badge bg-success">

                                            Yes

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            No

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($category->is_optional)

                                        <span class="badge bg-warning text-dark">

                                            Optional

                                        </span>

                                    @else

                                        <span class="badge bg-primary">

                                            Mandatory

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($category->status)

                                        <span class="badge bg-success">

                                            Active

                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            Inactive

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a href="{{ route('finance.fee-categories.edit', $category->id) }}"
                                           class="btn btn-sm btn-warning">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                        <form action="{{ route('finance.fee-categories.destroy', $category->id) }}"
                                              method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this category?')">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            @include('admin.finance.partials.table-empty', [
                                'colspan' => 8,
                                'message' => 'No fee categories found.'
                            ])

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-4">

                {{ $feeCategories->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
