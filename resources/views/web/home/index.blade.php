@extends('icore::web.layouts.layout')

@section('content')
@include('icore::web.partials.alerts-absolute')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <h1 class="display-4 text-white text-center">Fluid jumbotron</h1>
        <p class="lead text-white text-center">This is a modified jumbotron that occupies the
            entire horizontal space of its parent.</p>
        <form class="justify-content-center">
            <div class="input-group justify-content-center">
                <input type="text" class="border border-right-0 form-control-lg w-75" placeholder="Search...">
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary bg-primary border-left-0" type="button">
                        <i class="fa fa-search text-white"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
</div>
@endsection
