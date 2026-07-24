@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>Add Record</h2><div class="muted">SY {{ $grading?->sy }} | {{ $grading?->grading }}</div></div>
    <form method="post" action="{{ route('grades.consolidate') }}">@csrf<button class="btn warn" type="submit">Submit Grades To Registrar</button></form>
</div>
<div class="card">
    <table>
        <thead>
            <tr><th>Student</th><th>Grade</th><th>Section</th>@foreach($terms as $term)<th>{{ $term }}</th>@endforeach<th>Actions</th></tr>
        </thead>
        <tbody>
        @forelse($students as $st)
            <tr>
                <td>{{ $st->sname }}</td>
                <td>{{ $st->grade }}</td>
                <td>{{ $st->section }}</td>
                @foreach($terms as $term)
                    <td>
                        @if($statuses[$st->id][$term] ?? false)
                            <span class="badge ok">Done</span>
                        @else
                            <span class="badge bad">Missing</span>
                        @endif
                    </td>
                @endforeach
                <td>
                    @if($locked[$st->id] ?? false)
                        <span class="badge ok">Consolidated</span>
                    @else
                        <a class="btn" href="{{ route('grades.create', $st->id) }}">Input</a>
                    @endif
                    <a class="btn alt" href="{{ route('grades.review', $st->id) }}">View</a>
                    @if(!($locked[$st->id] ?? false))
                        <a class="btn warn" href="{{ route('grades.edit', $st->id) }}">Edit</a>
                    @endif
                    <a class="btn" href="{{ route('grades.print', ['student' => $st->id, 'autoprint' => 1]) }}" target="_blank" rel="noopener">Print View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ 5 + count($terms) }}">No assigned students.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
