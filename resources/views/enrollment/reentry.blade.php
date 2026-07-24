@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>Re-Entry Enrollment</h2></div>
    <p class="muted">Returning Student: {{ $student->sname }} ({{ $student->id }})</p>
    @php
        $gradeSections = $sections
            ->groupBy('grade_yr')
            ->map(fn ($rows) => $rows->pluck('section')->unique()->values())
            ->toArray();
        $selectedGrade = old('grade', $student->grade);
        $selectedSection = old('section', $student->section);
    @endphp
    <form method="post" action="{{ route('enrollment.reentry.store', $student->id) }}" class="grid two">
        @csrf
        <div>
            <label>Grade</label>
            <select name="grade" id="grade-select" required>
                @foreach(array_keys($gradeSections) as $g)
                    <option value="{{ $g }}" @selected($selectedGrade===$g)>{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Section</label>
            <select name="section" id="section-select" data-selected-section="{{ $selectedSection }}" required></select>
        </div>
        <div><label>Teacher</label><select name="uid" required>@foreach($teachers as $t)<option value="{{ $t->uid }}" @selected((string)$student->uid===(string)$t->uid)>{{ $t->fullname }}</option>@endforeach</select></div>
        <div style="align-self:end;"><button type="submit">Re-Enroll Student</button></div>
    </form>
</div>

<script>
    (() => {
        const gradeSections = @json($gradeSections);
        const gradeSelect = document.getElementById('grade-select');
        const sectionSelect = document.getElementById('section-select');

        if (!gradeSelect || !sectionSelect) {
            return;
        }

        const renderSections = () => {
            const grade = gradeSelect.value;
            const sections = gradeSections[grade] || [];
            const selectedBefore = sectionSelect.dataset.selectedSection || sectionSelect.value;

            sectionSelect.innerHTML = '';
            sections.forEach((section) => {
                const option = document.createElement('option');
                option.value = section;
                option.textContent = section;
                if (section === selectedBefore) {
                    option.selected = true;
                }
                sectionSelect.appendChild(option);
            });

            if (!sectionSelect.value && sections.length > 0) {
                sectionSelect.value = sections[0];
            }
            sectionSelect.dataset.selectedSection = '';
        };

        gradeSelect.addEventListener('change', renderSections);
        renderSections();
    })();
</script>
@endsection
