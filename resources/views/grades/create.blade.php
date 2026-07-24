@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>Input Grade</h2><div class="muted">{{ $student->sname }} | {{ $grading?->grading }} | {{ $grading?->sy }}</div></div>
    <form method="post" action="{{ route('grades.store', $student->id) }}">
        @csrf
        <table>
            <thead><tr><th>Learning Area</th><th>Grade</th></tr></thead>
            <tbody>
            @foreach($subjects as $idx => $subject)
                @php $key = 's' . ($idx + 1); @endphp
                <tr>
                    <td>{{ $subject->subject }}</td>
                    <td>
                        <input
                            name="{{ $key }}"
                            id="{{ $key }}"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            value="{{ old($key) }}"
                            @if($idx + 1 === 8) readonly @endif
                        >
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="margin-top:12px;"><button type="submit">Save Grade</button></div>
    </form>
</div>

<script>
    (() => {
        const ids = ['s9', 's10', 's11', 's12'];
        const mapehInput = document.getElementById('s8');
        if (!mapehInput) {
            return;
        }

        const computeMapeh = () => {
            const values = ids.map((id) => parseFloat(document.getElementById(id)?.value || '0'));
            const avg = values.reduce((a, b) => a + b, 0) / 4;
            mapehInput.value = Number.isFinite(avg) ? avg.toFixed(2) : '';
        };

        ids.forEach((id) => document.getElementById(id)?.addEventListener('input', computeMapeh));
        computeMapeh();
    })();
</script>
@endsection
