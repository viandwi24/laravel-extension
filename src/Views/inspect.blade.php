@extends('LaravelExtension::app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title" style="display: inline-block;">
            {{ $extension->config->name }}
            <small>
                <span class="badge {{ ($extension->active) ? "bg-success" : "bg-danger" }}">
                    {{ ($extension->active) ? "Enable" : "Disable" }}
                </span>
            </small>
        </h4>
        <a href="{{ route('extension.index') }}" class="btn btn-sm btn-primary float-right">< Back</a>
    </div>
    <div class="card-body">
        <h3># Description</h3>
        <p>{{ $extension->config->description }}</p>

        <h3># Version</h3>
        <p>v{{ $extension->config->version }}</p>

        <h3># Author</h3>
        <p>
            <span>{{ $extension->config->author->name }}</span>
            <small>
                <a target="_blank" href="{{ $extension->config->author->site }}">Open Author Website</a>
            </small>
        </p>

        <h3># Lifecycle</h3>
        <p class="mb-0">
            <b>Loaded : </b> <span class="badge {{ ($extension->loaded) ? "bg-success" : "bg-danger" }}">
                {{ ($extension->loaded) ? "True" : "False" }}
            </span>
        </p>
        <p class="mb-0">
            <b>Registered : </b> <span class="badge {{ ($extension->registered) ? "bg-success" : "bg-danger" }}">
                {{ ($extension->registered) ? "True" : "False" }}
            </span>
        </p>
        <p class="mb-0">
            <b>Booted : </b> <span class="badge {{ ($extension->booted) ? "bg-success" : "bg-danger" }}">
                {{ ($extension->booted) ? "True" : "False" }}
            </span>
        </p>
    </div>
    <div class="card-body" style="border-top: 1px solid rgb(185, 185, 185);">
        <h3>Error Trace</h3>
        {{ (\Symfony\Component\VarDumper\VarDumper::dump($extension->errors)) }}
    </div>
    <div class="card-body" style="border-top: 1px solid rgb(185, 185, 185);">
        <h3>Hook Action</h3>
        {{ (\Symfony\Component\VarDumper\VarDumper::dump($actions)) }}
        <h3>Hook Filter</h3>
        {{ (\Symfony\Component\VarDumper\VarDumper::dump($filters)) }}
    </div>
    <div class="card-body" style="border-top: 1px solid rgb(185, 185, 185);">
        <h3># Dump</h3>
        {{ (\Symfony\Component\VarDumper\VarDumper::dump($extension)) }}
        {{-- json_encode($extension, JSON_PRETTY_PRINT, 1024) --}}
    </div>
</div>    
@endsection