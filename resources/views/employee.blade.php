@extends('layout')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Employee Directory</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                All Employees
            </div>
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="employeeTable">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Employee Code</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->first_name }}" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><br>
                                <small class="text-muted">{{ $employee->email }}</small>
                            </td>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->designation }}</td>
                            <td>
                                {{ $employee->phone }}<br>
                                @if($employee->emergency_contact_name)
                                    <small class="text-muted">Emergency: {{ $employee->emergency_contact_name }} ({{ $employee->emergency_contact_phone }})</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $employee->employment_type == 'Full-time' ? 'success' : ($employee->employment_type == 'Part-time' ? 'warning' : 'info') }}">
                                    {{ $employee->employment_type }}
                                </span><br>
                                <small>Joined: {{ $employee->date_of_joining }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-placeholder {
        font-size: 14px;
        font-weight: bold;
    }
    #employeeTable th {
        white-space: nowrap;
    }
    .card-header {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search employees...",
            }
        });
    });
</script>
@endpush