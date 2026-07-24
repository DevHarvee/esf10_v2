@extends('layouts.app')

@section('content')
<div class="grid two">
    <div class="card">
        <div class="head"><h2>Create User Account</h2></div>
        <form method="post" action="{{ route('users.store') }}" class="grid">
            @csrf
            <div><label>Full Name</label><input name="fullname" required></div>
            <div><label>Email</label><input name="email" type="email" required></div>
            <div><label>Password</label><input name="pwd" type="password" required></div>
            <div><label>Role</label><select name="utype"><option value="teacher">Teacher</option><option value="admin">Admin</option></select></div>
            <button type="submit">Save Account</button>
        </form>
    </div>

    <div class="card">
        <div class="head"><h2>User Accounts</h2></div>
        <table>
            <thead><tr><th>Full Name</th><th>Email</th><th>Role</th><th>Update</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->fullname }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->utype }}</td>
                        <td>
                            <form method="post" action="{{ route('users.update', $u->uid) }}" class="grid">
                                @csrf
                                <input name="fullname" value="{{ $u->fullname }}" required>
                                <input name="email" value="{{ $u->email }}" required>
                                <input name="pwd" placeholder="New password (optional)">
                                <select name="utype"><option value="teacher" @selected($u->utype==='teacher')>Teacher</option><option value="admin" @selected($u->utype==='admin')>Admin</option></select>
                                <button type="submit" class="btn alt">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>
@endsection
