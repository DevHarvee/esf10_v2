@extends('layouts.app')

@section('content')
@php
    $gradeSections = $sections
        ->groupBy('grade_yr')
        ->map(fn ($rows) => $rows->pluck('section')->unique()->values())
        ->toArray();
@endphp
<div class="card">
    <div class="head"><h2>Create User Account</h2></div>
    <p class="muted" style="margin-top:-8px; margin-bottom:10px;">Set basic account details first. Teacher assignment is only required when role is Teacher.</p>
    <form method="post" action="{{ route('users.store') }}" class="grid">
        @csrf
        <div class="head" style="margin-bottom:0;"><h2 style="font-size:16px;">Account Details</h2></div>
        <div class="grid two">
            <div><label>Full Name</label><input name="fullname" required></div>
            <div><label>Email</label><input name="email" type="email" required></div>
            <div><label>Password</label><input name="pwd" type="password" required></div>
            <div><label>Role</label><select name="utype" class="role-select"><option value="teacher">Teacher</option><option value="admin">Admin</option></select></div>
        </div>

        <div class="head" style="margin-bottom:0;"><h2 style="font-size:16px;">Teacher Assignment</h2></div>
        <div class="grid two">
            <div>
                <label>Assigned Grade</label>
                <select name="assigned_grade" class="grade-select" data-section-target="create-section" data-selected="{{ old('assigned_grade') }}">
                    <option value="">Select Grade</option>
                    @foreach(array_keys($gradeSections) as $grade)
                        <option value="{{ $grade }}" @selected(old('assigned_grade') === $grade)>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Assigned Section</label>
                <select name="assigned_section" id="create-section" data-selected="{{ old('assigned_section') }}">
                    <option value="">Select Section</option>
                </select>
            </div>
        </div>
        <button type="submit">Save Account</button>
    </form>
</div>

<div class="card">
    <div class="head"><h2>User Accounts</h2></div>
        <table>
            <thead><tr><th>Full Name</th><th>Email</th><th>Role</th><th>Assigned</th><th>Action</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->fullname }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->utype }}</td>
                        <td>{{ $u->assigned_grade ? $u->assigned_grade . ' - ' . $u->assigned_section : 'N/A' }}</td>
                        <td>
                            <button type="button" class="btn alt" data-modal-open="user-modal-{{ $u->uid }}">Edit Profile</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
</div>

@foreach($users as $u)
    <div id="user-modal-{{ $u->uid }}" class="user-modal" hidden>
        <div class="user-modal__backdrop" data-modal-close="user-modal-{{ $u->uid }}"></div>
        <div class="user-modal__panel card" role="dialog" aria-modal="true" aria-label="Edit User Profile">
            <div class="head" style="margin-bottom:10px;">
                <h2 style="font-size:18px;">Update User Profile</h2>
                <button type="button" class="btn warn" data-modal-close="user-modal-{{ $u->uid }}">Close</button>
            </div>
            <form method="post" action="{{ route('users.update', $u->uid) }}" class="grid">
                @csrf
                <div class="grid two">
                    <div><label>Full Name</label><input name="fullname" value="{{ $u->fullname }}" required></div>
                    <div><label>Email</label><input name="email" value="{{ $u->email }}" required></div>
                    <div><label>New Password</label><input name="pwd" placeholder="Optional"></div>
                    <div><label>Role</label><select name="utype" class="role-select"><option value="teacher" @selected($u->utype==='teacher')>Teacher</option><option value="admin" @selected($u->utype==='admin')>Admin</option></select></div>
                </div>
                <div class="head" style="margin-bottom:0;"><h2 style="font-size:16px;">Teacher Assignment</h2></div>
                <div class="grid two">
                    <div>
                        <label>Assigned Grade</label>
                        <select name="assigned_grade" class="grade-select" data-section-target="section-{{ $u->uid }}" data-selected="{{ $u->assigned_grade }}">
                            <option value="">Select Grade</option>
                            @foreach(array_keys($gradeSections) as $grade)
                                <option value="{{ $grade }}" @selected($u->assigned_grade === $grade)>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Assigned Section</label>
                        <select name="assigned_section" id="section-{{ $u->uid }}" data-selected="{{ $u->assigned_section }}">
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn alt">Update Account</button>
            </form>
        </div>
    </div>
@endforeach

<style>
    .user-modal {
        position: fixed;
        inset: 0;
        z-index: 900;
        display: grid;
        place-items: center;
    }
    .user-modal[hidden] {
        display: none;
    }
    .user-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(20, 27, 45, 0.55);
    }
    .user-modal__panel {
        position: relative;
        width: min(760px, 94vw);
        max-height: 90vh;
        overflow: auto;
        margin: 0;
    }
</style>

<script>
    (() => {
        const gradeSections = @json($gradeSections);
        const modalOpeners = document.querySelectorAll('[data-modal-open]');
        const modalClosers = document.querySelectorAll('[data-modal-close]');

        const showModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        };

        const hideModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }
            modal.hidden = true;
            if (!document.querySelector('.user-modal:not([hidden])')) {
                document.body.style.overflow = '';
            }
        };

        modalOpeners.forEach((button) => {
            button.addEventListener('click', () => showModal(button.dataset.modalOpen));
        });

        modalClosers.forEach((button) => {
            button.addEventListener('click', () => hideModal(button.dataset.modalClose));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }
            document.querySelectorAll('.user-modal:not([hidden])').forEach((modal) => {
                modal.hidden = true;
            });
            document.body.style.overflow = '';
        });

        const fillSections = (gradeSelect) => {
            const targetId = gradeSelect.dataset.sectionTarget;
            if (!targetId) {
                return;
            }

            const sectionSelect = document.getElementById(targetId);
            if (!sectionSelect) {
                return;
            }

            const selectedBefore = sectionSelect.dataset.selected || sectionSelect.value;
            const grade = gradeSelect.value;
            const sections = gradeSections[grade] || [];

            sectionSelect.innerHTML = '<option value="">Select Section</option>';
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
            sectionSelect.dataset.selected = '';
        };

        document.querySelectorAll('.grade-select').forEach((gradeSelect) => {
            gradeSelect.addEventListener('change', () => fillSections(gradeSelect));
            fillSections(gradeSelect);
        });

        document.querySelectorAll('form').forEach((form) => {
            const roleSelect = form.querySelector('.role-select');
            const gradeSelect = form.querySelector('.grade-select');
            if (!roleSelect || !gradeSelect) {
                return;
            }
            const targetId = gradeSelect.dataset.sectionTarget;
            const sectionSelect = targetId ? document.getElementById(targetId) : null;

            const toggleTeacherFields = () => {
                const isTeacher = roleSelect.value === 'teacher';
                gradeSelect.disabled = !isTeacher;
                if (sectionSelect) {
                    sectionSelect.disabled = !isTeacher;
                }
            };

            roleSelect.addEventListener('change', toggleTeacherFields);
            toggleTeacherFields();
        });
    })();
</script>
@endsection
