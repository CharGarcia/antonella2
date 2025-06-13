

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Select de establecimiento --}}
        @if(Auth::check() && isset($establecimientos_disponibles))
            <li class="nav-item d-flex align-items-center">
                <i class="fas fa-building me-2 text-primary"> Establecimiento: </i>
                <select id="select-establecimiento" class="form-control form-control-sm select2" style="width: 220px;">
                    <option value="">Seleccione establecimiento</option>
                    @foreach($establecimientos_disponibles as $est)
                        <option value="{{ $est->id }}" {{ session('establecimiento_id') == $est->id ? 'selected' : '' }}>
                            {{ $est->nombre_comercial }}
                        </option>
                    @endforeach
                </select>
            </li>
        @endif

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>

{{-- Scripts para Select2 y AJAX --}}
@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Buscar establecimiento",
            width: '200px'
        });

        $('#select-establecimiento').on('change', function() {
            const establecimiento_id = $(this).val();

            $.ajax({
                url: "{{ route('establecimiento.cambiar') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    establecimiento_id: establecimiento_id
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = "{{ route('home') }}";
                        $('#menu-lateral').load(location.href + ' #menu-lateral > *');
                        Swal.fire({
                            icon: 'success',
                            title: 'Establecimiento cambiado',
                            toast: true,
                            timer: 1500,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
