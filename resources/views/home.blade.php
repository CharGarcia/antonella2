
@extends('adminlte::page')
@section('title', 'Home')

@section('content_header')
   <h1>CaMaGaRe</h1>
@stop

@section('content')
   <p>Welcome to this beautiful admin panel.</p>
<div class="card border-primary"
>

   <div class="card-body">
       <h4 class="card-title">Title</h4>
       <p class="card-text">Text</p>
   </div>
</div>
@stop

@section('css')
   {{-- Add here extra stylesheets --}}
   {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
   <script> console.log("CaMaGaRe"); </script>
@stop
