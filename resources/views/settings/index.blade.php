@extends('layouts.app')

@section('content')
<div class="grid two">
    <div class="card">
        <div class="head"><h2>Grading Settings</h2></div>
        <form method="post" action="{{ route('settings.grading') }}" class="grid">
            @csrf
            <div><label>Current Grading</label><input name="grading" value="{{ $grading?->grading }}" required></div>
            <div><label>School Year</label><input name="sy" value="{{ $grading?->sy }}" required></div>
            <div><label>Quarter Terms (newline)</label><textarea name="terms" rows="6">@foreach($terms as $t){{ $t->term_name }}
@endforeach</textarea></div>
            <button type="submit">Update Grading</button>
        </form>
    </div>

    <div class="card">
        <div class="head"><h2>Add Section</h2></div>
        <form method="post" action="{{ route('settings.sections') }}" class="grid">
            @csrf
            <div><label>Grade</label><select name="grade_yr"><option>Grade 7</option><option>Grade 8</option><option>Grade 9</option><option>Grade 10</option></select></div>
            <div><label>Section Name</label><input name="section" required></div>
            <button type="submit">Add Section</button>
        </form>
    </div>
</div>

<div class="grid two">
    <div class="card">
        <div class="head"><h2>Add Subject</h2></div>
        <form method="post" action="{{ route('settings.subjects') }}" class="grid">
            @csrf
            <div><label>Subject</label><input name="subject" required></div>
            <button type="submit">Add Subject</button>
        </form>
    </div>

    <div class="card">
        <div class="head"><h2>Current Catalog</h2></div>
        <table><thead><tr><th>Sections</th><th>Subjects</th></tr></thead><tbody>
            @php $maxRows = max(count($sections), count($subjects)); @endphp
            @for($i=0; $i<$maxRows; $i++)
                <tr>
                    <td>{{ $sections[$i]->grade_yr ?? '' }} {{ $sections[$i]->section ?? '' }}</td>
                    <td>{{ $subjects[$i]->subject ?? '' }}</td>
                </tr>
            @endfor
        </tbody></table>
    </div>
</div>
@endsection
