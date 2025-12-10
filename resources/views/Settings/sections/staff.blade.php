<link rel="stylesheet" href="{{ asset('css/Staff/staff_table.css') }}">
<table class="staff-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->status == 'active')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-secondary">Inactive</span>
                @endif
            </td>
            <td>{{ $user->role }}</td>
            <td class="actions-cell d-flex gap-2">
                <a href="{{ route('staff.edit', $user->id) }}" class="btn btn-sm btn-outline-success me-1">
                    <i class="bi bi-pencil"></i> Edit
                </a>

                <form action="{{ route('staff.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this.form)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>



                <form action="{{ route('staff.toggleStatus', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <button type="button"
                        class="btn btn-sm {{ $user->status === 'active' ? 'btn-outline-danger' : 'btn-primary' }}"
                        onclick="confirmStatusChange(this.form, '{{ $user->status === 'active' ? 'deactivate' : 'activate' }}')">

                        <i class="bi {{ $user->status === 'active' ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>

                </form>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script src="{{ asset('Js/staff.js') }}"></script>