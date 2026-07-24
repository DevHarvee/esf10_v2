@extends('layouts.app')

@section('content')
<div class="card">
    <div class="head"><h2>Add Student Records</h2></div>
    <form method="post" action="{{ route('enrollment.check') }}" class="grid two">
        @csrf
        <div>
            <label>Student ID / School ID</label>
            <input name="id" required>
        </div>
        <div style="align-self:end;">
            <button type="submit">Continue</button>
        </div>
    </form>
</div>
@endsection
