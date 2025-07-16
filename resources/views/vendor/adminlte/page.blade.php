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

    {{-- ✅ GLOBAL Datepicker Initialization --}}
    <script>
$(function () {
    moment.locale('es');

        $('.datetimepicker-input').each(function(){
            let input = $(this);
            let id = input.attr('id');
            let iconId = "#" + id + "Icon";

            if (!input.data('datetimepicker')) {
                input.datetimepicker({
                    format: 'DD/MM/YYYY',
                    locale: 'es',
                    icons: {
                        time: 'far fa-clock',
                        date: 'far fa-calendar',
                        up: 'fas fa-arrow-up',
                        down: 'fas fa-arrow-down',
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'far fa-calendar-check',
                        clear: 'far fa-trash-alt',
                        close: 'far fa-times-circle'
                    }
                });

                $(iconId).on('click', function(){
                    input.datetimepicker('show');
                });
            }

            // ✅ Aplica la máscara dd/mm/yyyy
            input.inputmask('datetime', {
                inputFormat: "dd/mm/yyyy",
                placeholder: "dd/mm/aaaa",
                showMaskOnHover: false,
                showMaskOnFocus: true,
                leapday: "29/02/",
                separator: '/',
                alias: "datetime"
            });
        });
    });

    </script>
@stop
