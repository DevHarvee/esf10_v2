@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head">
        <h2>Graduated Junior High Students</h2>
        <a href="{{ route('students.index') }}" class="btn alt">Back to Enrolled Students</a>
    </div>
    <form method="get" class="grid three">
        <div>
            <label>Search</label>
            <input name="q" value="{{ request('q') }}" placeholder="Name, ID, or LRN">
        </div>
        <div>
            <label>School Year</label>
            <select name="sy">
                <option value="">All School Years</option>
                @foreach($schoolYears as $schoolYear)
                    <option value="{{ $schoolYear }}" @selected(request('sy') === $schoolYear)>{{ $schoolYear }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; align-items:flex-end; gap:.5rem;">
            <button type="submit">Apply Filters</button>
            <a href="{{ route('students.graduates') }}" class="btn alt">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>LRN</th>
                <th>Sex</th>
                <th>Final Grade Level</th>
                <th>Section</th>
                <th>School Year</th>
                <th>Final Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($graduates as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->sname }}</td>
                <td>{{ $student->lrn }}</td>
                <td>{{ $student->sex }}</td>
                <td>{{ $student->grade }}</td>
                <td>{{ $student->section }}</td>
                <td>{{ $student->sy }}</td>
                <td>{{ $student->final_rating }}</td>
                <td><span class="badge ok">{{ $student->status }}</span></td>
                <td><a class="btn alt" href="{{ route('reports.permanent', $student->id) }}">View SF10</a></td>
            </tr>
        @empty
            <tr><td colspan="10">No graduate records found.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if(method_exists($graduates, 'links'))
        <div style="margin-top:1rem;">
            {{ $graduates->links() }}
        </div>
    @endif
</div>
@endsection