@extends('layouts.app')

@section('content')
@php
    $termOptions = $terms->pluck('term_name')->values()->all();
    $selectedGrading = old('grading', $grading?->grading ?: ($termOptions[0] ?? ''));
    $sectionsPager = $sections->appends(['subjects_page' => request('subjects_page')]);
    $subjectsPager = $subjects->appends(['sections_page' => request('sections_page')]);
@endphp

<div class="card" style="padding:14px 18px;">
    <div class="muted">Settings are grouped by feature: grading cycle, quarter terms, sections, and subjects.</div>
</div>

<div class="grid two">
    <div class="card">
        <div class="head"><h2>Grading Settings</h2></div>
        <form method="post" action="{{ route('settings.grading') }}" class="grid">
            @csrf
            <div>
                <label>Current Grading</label>
                <select name="grading" required>
                    @forelse($termOptions as $option)
                        <option value="{{ $option }}" @selected($selectedGrading === $option)>{{ $option }}</option>
                    @empty
                        <option value="">No terms available</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label>School Year</label>
                <input name="sy" value="{{ $grading?->sy }}" required>
            </div>
            <button type="submit">Update Grading</button>
        </form>
    </div>

    <div class="card">
        <div class="head"><h2>Quarter Terms</h2></div>
        <div class="muted" style="margin-bottom:.5rem;">Add, rename, or remove available grading terms.</div>
        <form method="post" action="{{ route('settings.terms') }}" class="grid" style="margin-bottom:1rem;">
            @csrf
            <div style="display:flex; gap:.5rem; align-items:center;">
                <input name="term_name" placeholder="New term name" required>
                <button type="submit" class="btn alt">Add Term</button>
            </div>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Term</th>
                    <th style="width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($terms as $term)
                    <tr>
                        <td>
                            <form method="post" action="{{ route('settings.terms.update', $term) }}" style="display:flex; gap:.5rem; align-items:center;">
                                @csrf
                                @method('put')
                                <input name="term_name" value="{{ $term->term_name }}" required>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="{{ route('settings.terms.delete', $term) }}" style="display:inline;">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn warn" onclick="return confirm('Remove this term?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2">No terms yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid two">
    <div class="card">
        <div class="head"><h2>Sections</h2></div>
        <div class="muted" style="margin-bottom:.5rem;">Create and manage section names by grade level.</div>
        <form method="post" action="{{ route('settings.sections') }}" class="grid" style="margin-bottom:1rem;">
            @csrf
            <div class="grid two">
                <div>
                    <label>Grade</label>
                    <select name="grade_yr">
                        <option>Grade 7</option>
                        <option>Grade 8</option>
                        <option>Grade 9</option>
                        <option>Grade 10</option>
                    </select>
                </div>
                <div>
                    <label>Section Name</label>
                    <input name="section" required>
                </div>
            </div>
            <button type="submit">Add Section</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sections as $section)
                    <tr>
                        <td>
                            <form method="post" action="{{ route('settings.sections.update', $section) }}" style="display:flex; gap:.5rem; align-items:center;">
                                @csrf
                                @method('put')
                                <select name="grade_yr">
                                    <option value="Grade 7" @selected($section->grade_yr === 'Grade 7')>Grade 7</option>
                                    <option value="Grade 8" @selected($section->grade_yr === 'Grade 8')>Grade 8</option>
                                    <option value="Grade 9" @selected($section->grade_yr === 'Grade 9')>Grade 9</option>
                                    <option value="Grade 10" @selected($section->grade_yr === 'Grade 10')>Grade 10</option>
                                </select>
                                <input name="section" value="{{ $section->section }}" required>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td style="white-space:nowrap;">
                            <form method="post" action="{{ route('settings.sections.delete', $section) }}" style="display:inline;">
                                @csrf
                                @method('delete')
                                <button type="submit" onclick="return confirm('Remove this section?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2">No sections yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($sectionsPager->hasPages())
            <div style="margin-top:1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap;">
                <span class="muted">Sections page {{ $sectionsPager->currentPage() }} of {{ $sectionsPager->lastPage() }}</span>
                <div style="display:flex; gap:.5rem; align-items:center;">
                    @if($sectionsPager->onFirstPage())
                        <span class="btn alt" style="opacity:.55; pointer-events:none;">Previous</span>
                    @else
                        <a class="btn alt" href="{{ $sectionsPager->previousPageUrl() }}">Previous</a>
                    @endif
                    @if($sectionsPager->hasMorePages())
                        <a class="btn alt" href="{{ $sectionsPager->nextPageUrl() }}">Next</a>
                    @else
                        <span class="btn alt" style="opacity:.55; pointer-events:none;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="head"><h2>Subjects</h2></div>
        <div class="muted" style="margin-bottom:.5rem;">Add and maintain subject names used in grade encoding.</div>
        <form method="post" action="{{ route('settings.subjects') }}" class="grid" style="margin-bottom:1rem;">
            @csrf
            <div>
                <label>Subject</label>
                <input name="subject" required>
            </div>
            <button type="submit">Add Subject</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <form method="post" action="{{ route('settings.subjects.update', $subject) }}" style="display:flex; gap:.5rem; align-items:center;">
                                @csrf
                                @method('put')
                                <input name="subject" value="{{ $subject->subject }}" required>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td style="white-space:nowrap;">
                            <form method="post" action="{{ route('settings.subjects.delete', $subject) }}" style="display:inline;">
                                @csrf
                                @method('delete')
                                <button type="submit" onclick="return confirm('Remove this subject?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2">No subjects yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($subjectsPager->hasPages())
            <div style="margin-top:1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap;">
                <span class="muted">Subjects page {{ $subjectsPager->currentPage() }} of {{ $subjectsPager->lastPage() }}</span>
                <div style="display:flex; gap:.5rem; align-items:center;">
                    @if($subjectsPager->onFirstPage())
                        <span class="btn alt" style="opacity:.55; pointer-events:none;">Previous</span>
                    @else
                        <a class="btn alt" href="{{ $subjectsPager->previousPageUrl() }}">Previous</a>
                    @endif
                    @if($subjectsPager->hasMorePages())
                        <a class="btn alt" href="{{ $subjectsPager->nextPageUrl() }}">Next</a>
                    @else
                        <span class="btn alt" style="opacity:.55; pointer-events:none;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
