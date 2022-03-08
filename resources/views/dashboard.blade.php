@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="btn btn-sm btn-primary" id="">
                    Top Up
                </div>
                Your Balance : {{Auth::user()->balance}}
            </div>
        </div>
    </div>
@endsection