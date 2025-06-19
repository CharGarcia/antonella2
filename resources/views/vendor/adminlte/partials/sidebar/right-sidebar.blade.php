<aside class="control-sidebar control-sidebar-{{ config('adminlte.right_sidebar_theme') }}" style="height: 100vh; overflow-y: auto;">
    <div class="p-3">
        <h5>Configuración</h5>
        <hr>
@hasrole('super_admin')
        @can('gestionar-empresas')
            <a href="{{ route('empresas.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Empresas
            </a>
        @endcan

        @can('gestionar-establecimientos')
            <a href="{{ route('establecimientos.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Establecimientos
            </a>
        @endcan

        @can('gestionar-menus')
            <a href="{{ route('menus.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Menús
            </a>
        @endcan

        @can('gestionar-submenus')
            <a href="{{ route('submenus.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> SubMenús
            </a>
        @endcan

        @can('gestionar-permisos')
            <a href="{{ route('permisos.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Permisos de roles
            </a>
        @endcan

        @can('gestionar-retenciones')
            <a href="{{ route('retenciones.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-clipboard-check mr-2"></i> Retenciones SRI
            </a>
        @endcan

        @can('gestionar-roles')
            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-universal-access mr-2"></i> Roles de usuarios
            </a>
        @endcan

        @can('gestionar-usuarios')
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-universal-access mr-2"></i> Gestión de usuarios
            </a>
        @endcan

        @can('gestionar-tarifasiva')
            <a href="{{ route('tarifa_iva.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Tarifas de IVA
            </a>
        @endcan

        @can('gestionar-formaspagosri')
            <a href="{{ route('formas_pago_sri.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Formas de pago SRI
            </a>
        @endcan

        @can('gestionar-asignacionestablecimientousuario')
            <a href="{{ route('asignacion_establecimiento_usuario.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Asignar establecimiento
            </a>
        @endcan

        @can('gestionar-usuario-asignado')
            <a href="{{ route('usuario_asignado.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Asignar usuarios
            </a>
        @endcan

    @endhasrole
       @hasrole('admin')
        @can('gestionar-asignacionestablecimientousuario-admin')
            <a href="{{ route('asignacion_establecimiento_usuario_admin.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-list-alt mr-2"></i> Asignar establecimiento
            </a>
        @endcan
        @can('gestionar-usuarios')
         <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start mb-2 w-100 text-nowrap">
                <i class="fas fa-universal-access mr-2"></i> Gestión de usuarios
            </a>
            @endcan
    @endhasrole
    </div>
</aside>
