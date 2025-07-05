@extends('adminlte::master')
@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
@vite(['resources/js/app.js'])
@livewireStyles
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses() . ' compact-ui')

@section('body_data', $layoutHelper->makeBodyData())
@section('body')
    <div class="wrapper">
        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif
    </div>
@stop

@section('adminlte_js')
    @livewireScripts
    <livewire:chatbot />
    @stack('js')
    @yield('js')

    {{-- SweetAlert2 Toast Notifications --}}
    @php
        $alertTypes = ['success', 'error', 'warning', 'info'];
    @endphp

    @foreach($alertTypes as $type)
        @if(session($type))
            <script>
    Swal.fire({
        icon: '{{ $type }}',
        title: '{{ ucfirst($type) }}',
        text: '{{ session($type) }}',
        position: 'center',
        customClass: {
            popup: 'swal-wide'
        },
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>

        @endif
    @endforeach
@stop
