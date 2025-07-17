<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif
<script>
    window.appData = {
        setSubmenuUrl: "{{ route('submenu.set') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                id="menu-lateral"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
                @isset($menus_nav)
                    @foreach ($menus_nav as $menu)
                        @php
                            $rutasSubmenu = $menu->submenus->pluck('ruta')->toArray();
                            $menuActivo = in_array(request()->route()->getName(), $rutasSubmenu);
                        @endphp

                        @if ($menu->submenus->isNotEmpty())
                            <li class="nav-item {{ $menuActivo ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link border border-primary rounded px-3 py-1 mb-1 {{ $menuActivo ? 'active' : '' }}">
                                    <i class="nav-icon {{ $menu->icono }}"></i>
                                    <p>
                                        {{ $menu->nombre }}
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>

                                <ul class="nav nav-treeview">
                                @foreach ($menu->submenus as $submenu)
                                    @php $ruta = $submenu->ruta; @endphp
                                    <li class="nav-item">
                                        @if(Route::has($ruta))
                                            <a href="{{ route($ruta) }}"
                                                class="nav-link submenu-link rounded mb-1
                                                    {{ request()->routeIs($ruta) ? 'bg-primary text-white' : 'bg-light text-secondary' }}"
                                                style="font-size: 0.90rem; padding: 4px 8px; max-width: 200px; border: 1px solid #dee2e6;"
                                                data-submenu-id="{{ $submenu->id }}">
                                                <i class="{{ $submenu->icono ?? 'far fa-circle' }} nav-icon me-2"
                                                    style="font-size: 0.75rem;
                                                    {{ request()->routeIs($ruta) ? 'color: white;' : 'color: #6c757d;' }}">
                                                </i>
                                                <p class="mb-0">{{ $submenu->nombre }}</p>
                                            </a>
                                        @else
                                            <a href="#" class="nav-link disabled border border-secondary rounded px-3 py-1 mb-1 text-muted">
                                                <i class="{{ $submenu->icono ?? 'far fa-circle' }} nav-icon me-2"></i>
                                                <p class="mb-0">{{ $submenu->nombre }}</p>
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                @endisset
            </ul>
        </nav>
    </div>
</aside>
<script>
   document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', '.submenu-link', function (e) {
        e.preventDefault();

        const submenuId = $(this).data('submenu-id');
        const href = $(this).attr('href');

        if (submenuId && href) {
            $.ajax({
                url: window.appData.setSubmenuUrl,
                type: 'POST',
                data: {
                    _token: window.appData.csrfToken,
                    submenu_id: submenuId
                },
                success: function () {
                    window.location.href = href;
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Error al cambiar de submenú.';
                    Swal.fire('Error', message, 'error');
                }
            });
        }
    });
});

</script>
