@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>Section Roster: {{ $section }}</h2><div class="muted">SY {{ $currentSy }}</div></div>
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>LRN</th><th>Birthdate</th><th>Sex</th></tr></thead>
        <tbody>
        @forelse($students as $st)
            <tr><td>{{ $st->id }}</td><td>{{ $st->sname }}</td><td>{{ $st->lrn }}</td><td>{{ $st->bday }}</td><td>{{ $st->sex }}</td></tr>
        @empty
            <tr><td colspan="5">No students found in this section.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
