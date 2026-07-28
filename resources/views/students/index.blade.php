@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head">
        <h2>All Enrolled Students</h2>
        <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
            <a href="{{ route('students.graduates') }}" class="btn alt">View Graduates</a>
            <div class="muted">Current SY: {{ $currentSy }}</div>
        </div>
    </div>
    <form method="get" class="grid three">
        <div>
            <label>Search</label>
            <input name="q" value="{{ request('q') }}" placeholder="Name, ID, or LRN">
        </div>
        <div>
            <label>Grade</label>
            <select name="grade">
                <option value="">All Grades</option>
                @foreach($gradeOptions as $grade)
                    <option value="{{ $grade }}" @selected(request('grade') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Section</label>
            <select name="section">
                <option value="">All Sections</option>
                @foreach($sectionOptions as $section)
                    <option value="{{ $section }}" @selected(request('section') === $section)>{{ $section }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Sex</label>
            <select name="sex">
                <option value="">All</option>
                @foreach($sexOptions as $sex)
                    <option value="{{ $sex }}" @selected(request('sex') === $sex)>{{ $sex }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; align-items:flex-end; gap:.5rem;">
            <button type="submit">Apply Filters</button>
            <a href="{{ route('students.index') }}" class="btn alt">Reset</a>
        </div>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Grade</th><th>Section</th><th>Sex</th><th>LRN</th><th>Adviser</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($students as $st)
            <tr>
                <td>{{ $st->id }}</td><td>{{ $st->sname }}</td><td>{{ $st->grade }}</td><td>{{ $st->section }}</td><td>{{ $st->sex }}</td><td>{{ $st->lrn }}</td><td>{{ $st->adviser?->fullname }}</td>
                <td><a class="btn alt" href="{{ route('reports.permanent', $st->id) }}">View SF10</a></td>
            </tr>
        @empty
            <tr><td colspan="8">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if(method_exists($students, 'links'))
        {{ $students->links() }}
    @endif
</div>
@endsection
