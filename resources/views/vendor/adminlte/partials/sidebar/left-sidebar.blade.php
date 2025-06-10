<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
                {{-- Configured sidebar links --}}
                {{-- @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item') --}}

                @isset($menus_nav)
    @foreach ($menus_nav as $menu)
        @if ($menu->submenus->isNotEmpty())
            <li class="nav-item {{ in_array(request()->route()->getName(), $menu->submenus->pluck('ruta')->toArray()) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array(request()->route()->getName(), $menu->submenus->pluck('ruta')->toArray()) ? 'active' : '' }}">
                    <i class="nav-icon {{ $menu->icono }}"></i>
                    <p>
                        {{ $menu->nombre }}
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- @foreach ($menu->submenus as $submenu)
                        <li class="nav-item">
                            <a href="{{ route($submenu->ruta) }}"
                               class="nav-link {{ request()->routeIs($submenu->ruta) ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ $submenu->nombre }}</p>
                            </a>
                        </li>
                    @endforeach --}}
                </ul>
            </li>
        @endif
    @endforeach
@endisset


            </ul>
        </nav>
    </div>

</aside>
