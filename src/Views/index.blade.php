@extends('LaravelExtension::app')

@section('content')
<div class="card">
    <div class="card-header">Extension</div>
    <div class="card-body p-0">
        @include('LaravelExtension::extension_list')
    </div>
</div>    
@endsection