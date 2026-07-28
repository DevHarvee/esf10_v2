@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>New Entry Enrollment</h2></div>
    @php
        $gradeSections = $sections
            ->groupBy('grade_yr')
            ->map(fn ($rows) => $rows->pluck('section')->unique()->values())
            ->toArray();
        $teacherMap = $teachers
            ->groupBy(fn ($teacher) => $teacher->assigned_grade . '||' . $teacher->assigned_section)
            ->map(fn ($rows) => $rows->map(fn ($teacher) => [
                'uid' => (string) $teacher->uid,
                'fullname' => $teacher->fullname,
            ])->values())
            ->toArray();
        $selectedGrade = old('grade', array_key_first($gradeSections));
        $selectedSection = old('section');
        $selectedTeacher = old('uid');
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
        <div>
            <label>Teacher</label>
            <select name="uid" id="teacher-select" data-selected-teacher="{{ $selectedTeacher }}" required></select>
            <div class="muted">Teachers are filtered by assigned grade and section.</div>
        </div>
        <div style="align-self:end;"><button type="submit">Enroll Student</button></div>
    </form>
</div>

<script>
    (() => {
        const gradeSections = @json($gradeSections);
        const teacherMap = @json($teacherMap);
        const gradeSelect = document.getElementById('grade-select');
        const sectionSelect = document.getElementById('section-select');
        const teacherSelect = document.getElementById('teacher-select');

        if (!gradeSelect || !sectionSelect || !teacherSelect) {
            return;
        }

        const renderTeachers = () => {
            const grade = gradeSelect.value;
            const section = sectionSelect.value;
            const selectedBefore = teacherSelect.dataset.selectedTeacher || teacherSelect.value;
            const key = `${grade}||${section}`;
            const teachers = teacherMap[key] || [];

            teacherSelect.innerHTML = '';
            if (teachers.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No assigned teacher for selected section';
                teacherSelect.appendChild(option);
                teacherSelect.dataset.selectedTeacher = '';
                return;
            }

            teachers.forEach((teacher) => {
                const option = document.createElement('option');
                option.value = teacher.uid;
                option.textContent = teacher.fullname;
                if (teacher.uid === selectedBefore) {
                    option.selected = true;
                }
                teacherSelect.appendChild(option);
            });

            if (!teacherSelect.value && teachers.length > 0) {
                teacherSelect.value = teachers[0].uid;
            }
            teacherSelect.dataset.selectedTeacher = '';
        };

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
            renderTeachers();
        };

        gradeSelect.addEventListener('change', renderSections);
        sectionSelect.addEventListener('change', renderTeachers);
        renderSections();
    })();
</script>
@endsection
