@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>New Entry Enrollment</h2></div>
    @php
        $gradeSections = $sections
            ->groupBy('grade_yr')
            ->map(fn ($rows) => $rows->pluck('section')->unique()->values())
            ->toArray();
        $selectedGrade = old('grade', array_key_first($gradeSections));
        $selectedSection = old('section');
    @endphp
    <form method="post" action="{{ route('enrollment.new.store') }}" class="grid two">
        @csrf
        <div><label>Student ID</label><input name="id" value="{{ $studentId }}" required></div>
        <div><label>LRN</label><input name="lrn" required></div>
        <div><label>First Name</label><input name="fname" required></div>
        <div><label>Middle Name</label><input name="mname" required></div>
        <div><label>Last Name</label><input name="lname" required></div>
        <div><label>Extension</label><input name="ext"></div>
        <div><label>Sex</label><select name="sex" required><option @selected(old('sex')==='Male')>Male</option><option @selected(old('sex')==='Female')>Female</option></select></div>
        <div><label>Birthdate</label><input type="date" name="bday" value="{{ old('bday') }}" required></div>
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
        <div><label>Teacher</label><select name="uid" required>@foreach($teachers as $t)<option value="{{ $t->uid }}">{{ $t->fullname }}</option>@endforeach</select></div>
        <div style="align-self:end;"><button type="submit">Enroll Student</button></div>
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
