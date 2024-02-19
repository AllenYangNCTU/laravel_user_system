@extends('members.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show members</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('members.index') }}"> Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>First name:</strong>
                {{ $member->first_name }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Last name:</strong>
                {{ $member->last_name }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $member->email }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Birthday:</strong>
                {{ $member->birthdate }}
            </div>
        </div>
        <h5>Residential Address</h5>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Address:</strong>
                {{ $residential->address }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Zipcode:</strong>
                {{ $residential->zipcode }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>City:</strong>
                {{ $residential->city }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>State:</strong>
                {{ $residential->state }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Country:</strong>
                {{ $residential->country }}
            </div>
        </div>
        <br>
        <h5>Correspondence Address</h5>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Address:</strong>
                {{ $correspondence->address }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Zipcode:</strong>
                {{ $correspondence->zipcode }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>City:</strong>
                {{ $correspondence->city }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>State:</strong>
                {{ $correspondence->state }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Country:</strong>
                {{ $correspondence->country }}
            </div>
        </div>
    </div>
@endsection
