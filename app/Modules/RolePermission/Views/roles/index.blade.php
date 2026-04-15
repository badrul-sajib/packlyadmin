@extends('backend.app')
@section('content')

<div class="page-title mb-3">
    <div>
        <h5 class="fw-600">Roles</h5>
    </div>
</div>

<div class="page-wrapper mt-3">
    <div class="page-title mb-3">
        @permission('role-create')
        <a href="{{ route('roles.create') }}"
            class="btn btn-primary btn-sm justify-content-center d-flex align-items-center"
            style="width: fit-content">Add new</a>
        @endpermission
    </div>


    <div class="table-wrapper">
        <div class="table">
            <div class="thead">
                <div class="row">
                    <div class="cell" data-width="54px" style="width: 54px"> SL </div>
                    <div class="cell" data-width="150px" style="width: 150px"> Name </div>
                    <div class="cell" data-width="450px" style="width: 450px"> Permissions </div>
                    <div class="cell" data-width="100px" style="width: 100px"> Guard Name </div>
                    @permission(['role-update', 'role-delete'])
                    <div class="cell" data-width="160px" style="width: 160px;"> Action</div>
                    @endpermission
                </div>
            </div>
            <div id="table" class="tbody">
                @foreach ($roles as $role)
                <div class="row">
                    <div class="cell" data-width="54px" style="width: 54px"> {{ $loop->iteration }} </div>
                    <div class="cell" data-width="150px" style="width: 150px"> {{ $role->name }} </div>
                    <div class="cell" data-width="450px" style="width: 450px">
                        @php
                            $permissions = $role->permissions;
                            $previewPermissions = $permissions->take(3);
                            $remainingCount = $permissions->count() - $previewPermissions->count();
                        @endphp

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            @forelse ($previewPermissions as $value)
                                <span>
                                    <span
                                        style="width: 10px;background: red;display: inline-block;height: 10px;border-radius: 6px;"></span>
                                    {{ $value->name }}
                                </span>
                            @empty
                                <span class="text-muted">No permissions</span>
                            @endforelse

                            @if ($remainingCount > 0)
                                <button type="button" class="btn btn-link p-0 text-primary see-more-permissions"
                                    data-bs-toggle="modal" data-bs-target="#rolePermissionsModal-{{ $role->id }}">
                                    See More ({{ $remainingCount }})
                                </button>
                            @endif
                        </div>

                    </div>
                    <div class="cell" data-width="100px" style="width: 100px"> {{ $role->guard_name }} </div>
                    @permission(['role-update', 'role-delete'])
                    <div class="cell" data-width="160px" style="width: 160px;">
                        <div class=" d-flex gap-2">
                            @permission('role-update')
                            <a href="{{ route('roles.edit', $role->id) }}"
                                class="btn btn-primary btn-sm justify-content-center align-items-center d-flex">Edit</a>
                            @endpermission
                            @permission('role-delete')
                                <button type="button" class="btn btn-warning btn-sm deleteRole"
                                    data-url="{{ route('roles.destroy', $role->id) }}">Delete</button>
                            @endpermission
                        </div>
                    </div>
                    @endpermission
                </div>

                @if ($permissions->count() > 0)
                    <div class="modal fade" id="rolePermissionsModal-{{ $role->id }}" tabindex="-1"
                        aria-labelledby="rolePermissionsModalLabel-{{ $role->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rolePermissionsModalLabel-{{ $role->id }}">
                                        Permissions for {{ $role->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        @foreach ($permissions as $value)
                                            <span class="badge bg-light text-dark">
                                                {{ $value->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).on('click', '.deleteRole', async function() {
        const url = $(this).data('url');
        const result = await AllScript.deleteItem(
            url,
            null,
            "DELETE",
            "Are you sure you want to delete this role?"
        );

        if (result) {
            setTimeout(() => {
                location.reload();
            }, 700);
        }
    });
</script>

@endpush
