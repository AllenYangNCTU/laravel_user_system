@extends('members.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>User system</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('members.create') }}"> Create New member</a>
                <a href="{{ route('members.export', ['members' => $members->pluck('id')->implode(',')]) }}"
                    class="btn btn-primary">Export</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ route('members.search') }}" method="GET">
        <div class="form-group">
            <input type="text" name="name" placeholder="姓名搜索">
        </div>
        <div class="form-group">
            <input type="text" name="email" placeholder="搜索">
        </div>
        <button type="submit">搜索</button>
    </form>


    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>First name</th>
            <th>last name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($members as $member)
            <tr>
                <td>{{ $member->id }}</td>
                <td>{{ $member->first_name }}</td>
                <td>{{ $member->last_name }}</td>
                <td>
                    <form action="{{ route('members.destroy', $member->id) }}" method="POST">

                        <a class="btn btn-info" href="{{ route('members.show', $member->id) }}">Show</a>

                        <a class="btn btn-primary" href="{{ route('members.edit', $member->id) }}">Edit</a>

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {!! $members->links() !!}
@endsection
