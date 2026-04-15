@extends('backend.app')
@section('content')
    <div class="page-title mb-3">
        <div>
            <h5 class="fw-600">User List</h5>
        </div>
    </div>

    <div class="page-wrapper mt-3">

        <div class="page-title mb-3 d-flex justify-content-between SelectSearch">
            @permission('user-create')
                <a href="{{ route('users.create') }}"
                    class="btn btn-primary btn-sm justify-content-center d-flex align-items-center">Add new</a>
            @endpermission

            <div class="serchBar w-25">
                <input type="text" class="form-control" placeholder="Search ..."
                    onChange="location.href='{{ current_url() }}?search='+this.value">
            </div>
        </div>


        <div class="table-wrapper">
            <div class="table">
                <div class="thead">
                    <div class="row">
                        <div class="cell" data-width="54px" style="width: 54px"> ID </div>
                        <div class="cell" data-width="150px" style="width: 150px"> Name </div>
                        <div class="cell" data-width="150px" style="width: 150px"> Email</div>
                        <div class="cell" data-width="150px" style="width: 130px"> Phone</div>
                        <div class="cell" data-width="150px" style="width: 130px"> Role</div>
                        <div class="cell" data-width="350px" style="width: 350px"> Permission</div>
                        {{-- <div class="cell" data-width="100px" style="width: 100px">  Status </div> --}}
                        @permission(['user-update', 'user-delete'])
                            <div class="cell" data-width="160px" style="width: 160px;"> Action</div>
                        @endpermission
                    </div>
                </div>
                <div id="table" class="tbody">
               
                    @foreach ($users as $key => $user)
                        <div class="row">
                            <div class="cell" data-width="54px" style="width: 54px"> {{ $user->id }} </div>
                            <div class="cell" data-width="150px" style="width: 150px"> {{ $user->name }} </div>
                            <div class="cell" data-width="150px" style="width: 150px"> {{ $user->email }}</div>
                            <div class="cell" data-width="150px" style="width: 150px"> {{ $user->phone }}</div>
                            <div class="cell" data-width="150px" style="width: 150px">
                                @permission('user-update')
                                <select class="form-select form-select-sm role-change-select"
                                    data-user-id="{{ $user->id }}"
                                    data-url="{{ route('users.change-role', $user->id) }}">
                                    <option value="">-- No Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @endpermission
                            </div>
                            <div class="cell" data-width="350px" style="width: 350px">
                                @php
                                    $permissions = $user->permissions
                                        ->merge($user->roles->flatMap->permissions)
                                        ->unique('id');
                                @endphp

                                <div
                                    style="display: flex; flex-wrap: wrap; gap: 10px; max-height: 200px; overflow-y: auto; width: 100%; box-sizing: border-box;">
                                    @foreach ($permissions as $permission)
                                        <span class="d-flex align-items-center bg-light rounded-pill px-2 py-1"
                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1 1 auto; min-width: 150px; box-sizing: border-box;">
                                            <span class="me-2"
                                                style="width: 8px; height: 8px; min-width: 8px;
                                                    background: {{ $user->permissions->contains('id', $permission->id) ? 'red' : 'blue' }};
                                                    display: inline-block; border-radius: 50%;"></span>
                                            <span style="font-size: 0.9em;">{{ $permission->name }}</span>
                                        </span>
                                    @endforeach
                                </div>

                            </div>

                            {{-- <div class="cell" data-width="100px" style="width: 100px">  {{ $item->status }}
                </div> --}}
                            @permission(['user-update', 'user-delete'])
                                <div class="cell" data-width="260px" style="width: 260px;">
                                    <div class="d-flex gap-2">
                                        {{-- Reset Password --}}

                                        @permission('user-update')
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-primary btn-sm justify-content-center align-items-center d-flex">Edit</a>
                                        @endpermission
                                        @permission('user-delete')
                                            <button type="button" class="btn btn-warning btn-sm deleteItem"
                                                data-url="{{ route('users.destroy', $user->id) }}">Delete</button>
                                        @endpermission
                                    </div>

                                    @permission('user-update-reset-password')
                                        <form id="resetForm" action="{{ route('users.reset-password', $user->id) }}"
                                            method="post">
                                            @method('PATCH')
                                            @csrf
                                            <button type="button" class="btn btn-primary btn-sm text-white d-inline ms-2 ResetBtn">
                                                <i class="fa-solid fa-key"></i>
                                                Reset
                                            </button>
                                        </form>
                                    @endpermission
                                </div>
                            @endpermission


                        </div>
                    @endforeach
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.deleteItem', async function() {
            const url = $(this).data('url');
            const result = await AllScript.deleteItem(url);
            if (result) {
                setTimeout(() => {
                    location.reload();
                }, 700);
            }
        });

        $(document).on('change', '.role-change-select', function() {
            const select   = $(this);
            const url      = select.data('url');
            const roleId   = select.val();
            const roleName = select.find('option:selected').text().trim();
            const prevVal  = select.data('prev-val') ?? select.find('option[selected]').val() ?? '';

            Swal.fire({
                title: 'Change Role?',
                text: roleId
                    ? `Assign role "${roleName}" to this user?`
                    : 'Remove the current role from this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!',
            }).then((result) => {
                if (!result.isConfirmed) {
                    select.val(prevVal);
                    return;
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'PATCH',
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        role_id: roleId,
                    },
                    success: function(resp) {
                        if (resp.success) {
                            select.data('prev-val', roleId);
                            Swal.fire({ title: 'Done!', text: resp.message, icon: 'success', timer: 2000, showConfirmButton: false });
                        } else {
                            select.val(prevVal);
                            Swal.fire({ title: 'Error', text: resp.message || 'Failed to change role.', icon: 'error' });
                        }
                    },
                    error: function(xhr) {
                        select.val(prevVal);
                        const msg = xhr.responseJSON?.message || 'Something went wrong.';
                        Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                    },
                });
            });
        });

        // Track initial values
        $('.role-change-select').each(function() {
            $(this).data('prev-val', $(this).val());
        });

        $(document).on('click', '.ResetBtn', function() {
            Swal.fire({
                title: "Are you sure?",
                text: 'You want to send temporary password to this user phone number',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Send it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#resetForm').submit();
                }
            });
        });
    </script>

    @if (session('message'))
        <script>
            Swal.fire({
                title: "Success!",
                text: "{{ session('message') }}",
                icon: "success",
                timer: 3000,
                showConfirmButton: false,
                showCloseButton: true,
            });
        </script>
    @endif
@endpush
