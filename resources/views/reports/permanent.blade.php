@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head">
        <h2>Learner Permanent Record (SF10-JHS)</h2>
        <div>
            <a class="btn alt" href="{{ route('reports.print', ['student' => $student->id, 'autoprint' => 1]) }}" target="_blank" rel="noopener">Print View</a>
            <a class="btn" href="{{ route('reports.export', $student->id) }}">Export Excel</a>
        </div>
    </div>
    <div class="grid two">
        <div><strong>Learner:</strong> {{ $student->lname }}, {{ $student->fname }} {{ $student->mname }} {{ $student->ext }}</div>
        <div><strong>LRN:</strong> {{ $student->lrn }}</div>
        <div><strong>Birthdate:</strong> {{ $student->bday }}</div>
        <div><strong>Sex:</strong> {{ $student->sex }}</div>
    </div>
</div>

@foreach($records as $sy => $rows)
<div class="card">
    <div class="head"><h2>School Year {{ $sy }}</h2><div class="muted">Section {{ $student->section }}</div></div>
    <table>
        <thead><tr><th>Learning Area</th>@foreach($terms as $term)<th>{{ $term }}</th>@endforeach<th>Final</th><th>Remarks</th></tr></thead>
        <tbody>
            @foreach($subjects as $idx => $subject)
                @php $key = 's'.($idx+1); $vals = []; @endphp
                <tr>
                    <td>{{ $subject->subject }}</td>
                    @foreach($terms as $term)
                        @php
                            $row = $rows->get($term);
                            $v = data_get($row, $key);
                            if ($key === 's8' && $row) {
                                $v = round((((float) data_get($row, 's9')) + ((float) data_get($row, 's10')) + ((float) data_get($row, 's11')) + ((float) data_get($row, 's12'))) / 4, 2);
                            }
                        @endphp
                        <td @if((float)$v < 75 && $v !== null && $v !== '') style="color:#c53030;font-weight:700;" @endif>{{ $v }}</td>
                        @php if($v !== null && $v !== '') { $vals[] = (float)$v; } @endphp
                    @endforeach
                    @php $final = count($vals) ? round(array_sum($vals) / count($vals), 2) : null; @endphp
                    <td>{{ $final }}</td>
                    <td>{{ $final !== null && $final >= 75 ? 'PASSED' : 'FAILED' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection
