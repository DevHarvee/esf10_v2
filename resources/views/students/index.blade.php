@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>All Enrolled Students</h2><div class="muted">Current SY: {{ $currentSy }}</div></div>
    <form method="get" class="grid three">
        <input name="q" value="{{ request('q') }}" placeholder="Search name, ID, or LRN">
        <input name="grade" value="{{ request('grade') }}" placeholder="Grade">
        <input name="section" value="{{ request('section') }}" placeholder="Section">
        <button type="submit">Filter</button>
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
