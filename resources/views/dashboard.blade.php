@extends('layouts.app')

@section('content')
<div class="head">
    <div>
        <h2>Control Panel</h2>
        <div class="muted">{{ auth()->user()->fullname }} | {{ strtoupper(auth()->user()->utype) }}</div>
    </div>
    <div class="badge ok">SY {{ $summary['current_sy'] }} | {{ $summary['current_term'] }}</div>
</div>

<div class="grid three">
    <div class="card"><div class="muted">Total Student Records</div><h2>{{ $summary['total_students'] }}</h2></div>
    <div class="card"><div class="muted">Total User Accounts</div><h2>{{ $summary['total_users'] }}</h2></div>
    <div class="card"><div class="muted">Enrolled This SY</div><h2>{{ $summary['enrolled_this_sy'] }}</h2></div>
</div>

<div class="grid two">
    <div class="card"><div class="muted">Male</div><h2>{{ $summary['male'] }}</h2></div>
    <div class="card"><div class="muted">Female</div><h2>{{ $summary['female'] }}</h2></div>
</div>
@endsection
