<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermisosController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\RetencionSriController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SubMenuController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\EstablecimientoController;
use App\Http\Controllers\Admin\TarifaIvaController;
use App\Http\Controllers\Admin\FormasPagoSriController;
use App\Http\Controllers\Admin\AsignacionEstablecimientoUsuarioController;
use App\Http\Controllers\Admin\AsignacionEstablecimientoUsuarioAdminController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Admin\UsuarioAsignadoController;
use App\Http\Controllers\Empresa\ClienteController;
use App\Http\Controllers\Empresa\VendedorController;
use App\Http\Controllers\Empresa\CompradorController;
use App\Http\Controllers\empresa\ProveedorController;
use App\Http\Controllers\Empresa\PersonaController;
use App\Http\Controllers\Empresa\CategoriaController;
use App\Http\Controllers\Empresa\ListaPreciosController;


Route::get('/', function () {
    return view('auth/login');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});

//para cargar el select de las establecimientos
Route::post('/establecimiento/cambiar', [EstablecimientoController::class, 'cambiar'])->name('establecimiento.cambiar');


//para registrar los datos del nuevo usuario cuando le envian el link desde crear usuarios nuevos
Route::get('completar-registro/{token}', [RegistroController::class, 'mostrarFormulario'])->name('completar-registro.formulario');
Route::post('completar-registro/{token}', [RegistroController::class, 'guardarDatos'])->name('completar-registro.guardar');

//ruta de persona
Route::middleware(['auth'])
    ->prefix('empresa/personas')
    ->name('personas.')
    ->group(function () {
        Route::get('/buscar-identificacion', [PersonaController::class, 'buscarPorIdentificacion'])
            ->name('buscarPorIdentificacion');
    });



//para vendedores
//permiso admin user: gestionar-vendedores
//ruta para submenus: vendedores.vendedores
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-vendedores'])
    ->prefix('empresa/vendedores')
    ->name('vendedores.')
    ->group(function () {
        Route::get('/', [VendedorController::class, 'index'])->name('vendedores');
        Route::get('/data', [VendedorController::class, 'getData'])->name('data');
        Route::get('/crear', [VendedorController::class, 'create'])->name('create');
        Route::post('/', [VendedorController::class, 'store'])->name('store');
        Route::get('/{vendedor}/edit', [VendedorController::class, 'edit'])->name('edit');
        Route::put('/{vendedor}', [VendedorController::class, 'update'])->name('update');
        Route::delete('/{vendedor}', [VendedorController::class, 'destroy'])->name('destroy');
    });

//para compradores
//permiso admin user: gestionar-compradores
//ruta para submenus: compradores.compradores
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-compradores'])
    ->prefix('empresa/compradores')
    ->name('compradores.')
    ->group(function () {
        Route::get('/', [CompradorController::class, 'index'])->name('compradores');
        Route::get('/data', [CompradorController::class, 'getData'])->name('data');
        Route::get('/crear', [CompradorController::class, 'create'])->name('create');
        Route::post('/', [CompradorController::class, 'store'])->name('store');
        Route::get('/{comprador}/edit', [CompradorController::class, 'edit'])->name('edit');
        Route::put('/{comprador}', [CompradorController::class, 'update'])->name('update');
        Route::delete('/{comprador}', [CompradorController::class, 'destroy'])->name('destroy');
    });


// para clientes
//permiso admin user: gestionar-clientes
//ruta para submenus: clientes.clientes
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-clientes'])
    ->prefix('empresa/clientes')
    ->name('clientes.')
    ->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('clientes');
        Route::get('/data', [ClienteController::class, 'getData'])->name('data');
        Route::get('/crear', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
        Route::delete('/documentos/{documento}', [ClienteController::class, 'eliminarDocumento'])->name('documentos.eliminar');
    });


// para proveedores
//permiso admin user: gestionar-proveedores
//ruta para submenus: proveedores.proveedores
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-proveedores'])
    ->prefix('empresa/proveedores')
    ->name('proveedores.')
    ->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedores');
        Route::get('/data', [ProveedorController::class, 'getData'])->name('data');
        Route::get('/crear', [ProveedorController::class, 'create'])->name('create');
        Route::post('/', [ProveedorController::class, 'store'])->name('store');
        Route::get('/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('edit');
        Route::put('/{proveedor}', [ProveedorController::class, 'update'])->name('update');
        Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('destroy');
        Route::delete('/documentos/{documento}', [ProveedorController::class, 'eliminarDocumento'])->name('documentos.eliminar');
    });

//rutas para asignar usuarios a usuarios admin
Route::middleware(['auth'])
    ->prefix('usuario-asignado')
    ->name('usuario_asignado.')
    ->middleware('can:gestionar-usuario-asignado')
    ->group(function () {
        Route::get('/', [UsuarioAsignadoController::class, 'index'])->name('index');
        Route::get('/data', [UsuarioAsignadoController::class, 'getData'])->name('data');
        Route::post('/asignar', [UsuarioAsignadoController::class, 'store'])->name('store');
        Route::delete('/eliminar/{id}', [UsuarioAsignadoController::class, 'destroy'])->name('eliminar');
    });


//para asignar establecimientos a usuarios administrados por admin
//permiso admin: gestionar-asignacionestablecimientousuario-admin
Route::middleware(['auth'])
    ->prefix('asignacion_establecimiento_usuario_admin')
    ->name('asignacion_establecimiento_usuario_admin.')
    ->middleware('can:gestionar-asignacionestablecimientousuario-admin')
    ->group(function () {
        Route::get('/', [AsignacionEstablecimientoUsuarioAdminController::class, 'index'])->name('index');
        Route::get('/data', [AsignacionEstablecimientoUsuarioAdminController::class, 'getData'])->name('data');
        Route::get('/establecimientos-usuario/{id}', [AsignacionEstablecimientoUsuarioAdminController::class, 'getEstablecimientosUsuario'])->name('get_establecimientos_usuario');
        Route::post('/asignar', [AsignacionEstablecimientoUsuarioAdminController::class, 'asignarEstablecimientos'])->name('asignar');
        Route::delete('/eliminar/{id}', [AsignacionEstablecimientoUsuarioAdminController::class, 'eliminarAsignacion'])->name('eliminar');
        Route::post('/permisos', [AsignacionEstablecimientoUsuarioAdminController::class, 'verPermisos'])->name('permisos');
        Route::post('/permisos/guardar', [AsignacionEstablecimientoUsuarioAdminController::class, 'guardarPermisos'])->name('guardar_permisos');
    });


//para asignar establecimientos a los usuarios de super_admin
Route::middleware(['auth'])->group(function () {
    Route::prefix('asignacion_establecimiento_usuario')
        ->name('asignacion_establecimiento_usuario.')
        ->middleware('can:gestionar-asignacionestablecimientousuario')
        ->group(function () {
            Route::get('/', [AsignacionEstablecimientoUsuarioController::class, 'index'])->name('index');
            Route::get('/data', [AsignacionEstablecimientoUsuarioController::class, 'getData'])->name('data');
            Route::get('/establecimientos-usuario/{id}', [AsignacionEstablecimientoUsuarioController::class, 'getEstablecimientosUsuario'])->name('get_establecimientos_usuario');
            Route::post('/asignar', [AsignacionEstablecimientoUsuarioController::class, 'asignarEstablecimientos'])->name('asignar');
            Route::delete('/eliminar/{id}', [AsignacionEstablecimientoUsuarioController::class, 'eliminarAsignacion'])->name('eliminar');
            Route::get('/permisos/{id}', [AsignacionEstablecimientoUsuarioController::class, 'verPermisos'])->name('permisos');
            Route::post('/permisos/guardar', [AsignacionEstablecimientoUsuarioController::class, 'guardarPermisos'])->name('guardar_permisos');
        });
});


//para las formas_pago_sri
Route::middleware(['auth'])->group(function () {
    Route::prefix('formas_pago_sri')->name('formas_pago_sri.')->middleware('can:gestionar-formaspagosri')->group(function () {
        Route::get('/', [FormasPagoSriController::class, 'index'])->name('index');
        Route::get('/data', [FormasPagoSriController::class, 'getData'])->name('data');
        Route::post('/store', [FormasPagoSriController::class, 'store'])->name('store');
        Route::put('/update/{formaPagoSri}', [FormasPagoSriController::class, 'update'])->name('update');
        Route::get('/{formaPagoSri}', [FormasPagoSriController::class, 'show'])->name('show');
    });
});

//para las tarifas de iva
Route::middleware(['auth'])->group(function () {
    Route::prefix('tarifa_iva')->name('tarifa_iva.')->middleware('can:gestionar-tarifasiva')->group(function () {
        Route::get('/', [TarifaIvaController::class, 'index'])->name('index');
        Route::get('/data', [TarifaIvaController::class, 'getData'])->name('data');
        Route::post('/store', [TarifaIvaController::class, 'store'])->name('store');
        Route::put('/update/{tarifa}', [TarifaIvaController::class, 'update'])->name('update');
        Route::get('/{tarifa}', [TarifaIvaController::class, 'show'])->name('show');
        Route::delete('/{tarifa}', [TarifaIvaController::class, 'destroy'])->name('destroy');
    });
});


//para crear establecimientos
Route::middleware(['auth'])->group(function () {
    Route::prefix('establecimientos')->name('establecimientos.')->middleware('can:gestionar-establecimientos')->group(function () {
        Route::get('/', [EstablecimientoController::class, 'index'])->name('index');
        Route::get('/data', [EstablecimientoController::class, 'getData'])->name('data');
        Route::post('/store', [EstablecimientoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [EstablecimientoController::class, 'edit'])->name('edit');
        Route::put('/update/{establecimiento}', [EstablecimientoController::class, 'update'])->name('update');
        Route::get('/{establecimiento}', [EstablecimientoController::class, 'show'])->name('show');
    });
});


//para crear empresas
Route::middleware(['auth'])->group(function () {
    Route::prefix('empresas')->name('empresas.')->middleware('can:gestionar-empresas')->group(function () {
        Route::get('/buscar', [EmpresaController::class, 'buscar'])->name('buscar');
        Route::get('/', [EmpresaController::class, 'index'])->name('index');
        Route::get('/data', [EmpresaController::class, 'getData'])->name('data');
        Route::post('/store', [EmpresaController::class, 'store'])->name('store');
        Route::put('/update/{empresa}', [EmpresaController::class, 'update'])->name('update');
        Route::get('/{empresa}', [EmpresaController::class, 'show'])->name('show');
    });
});

//para menus
Route::middleware(['auth'])->group(function () {
    Route::prefix('menus')->name('menus.')->middleware('can:gestionar-menus')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/data', [MenuController::class, 'getMenu'])->name('data');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::put('/update/{menu}', [MenuController::class, 'update'])->name('update');
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
    });
});

//para submenus
Route::middleware(['auth'])->group(function () {
    Route::prefix('submenus')->name('submenus.')->middleware('can:gestionar-submenus')->group(function () {
        Route::get('/', [SubmenuController::class, 'index'])->name('index');
        Route::get('/data', [SubmenuController::class, 'getSubmenus'])->name('data');
        Route::post('/store', [SubmenuController::class, 'store'])->name('store');
        Route::put('/update/{submenu}', [SubmenuController::class, 'update'])->name('update');
        Route::get('/{submenu}', [SubmenuController::class, 'show'])->name('show');
        Route::delete('/{submenu}', [SubmenuController::class, 'destroy'])->name('destroy');
    });
    // Ruta general para establecer submenu activo
    Route::post('/submenu/set', [SubmenuController::class, 'set'])->name('submenu.set');
});


//para gestionar roles de usuarios
Route::middleware(['auth'])->group(function () {
    Route::prefix('roles')->name('roles.')->middleware('can:gestionar-roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/data', [RoleController::class, 'getUsers'])->name('data');
        Route::post('/assign', [RoleController::class, 'assignRole'])->name('assign');
    });
});

//para gestionar los permisos de roles sobre los usuarios
Route::middleware(['auth'])->group(function () {
    Route::prefix('permisos')->name('permisos.')->middleware('can:gestionar-permisos')->group(function () {
        Route::get('/', [PermisosController::class, 'index'])->name('index');
        Route::get('/data', [PermisosController::class, 'getRoles'])->name('data');
        Route::post('/guardar', [PermisosController::class, 'guardarPermiso'])->name('guardar');
        Route::post('/eliminar', [PermisosController::class, 'eliminarPermiso'])->name('eliminar');
    });
});

//para gestionar los usuarios
// permiso admin: gestionar-usuarios
Route::middleware(['auth'])->group(function () {
    Route::prefix('usuarios')->name('usuarios.')->middleware('can:gestionar-usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('index');
        Route::get('/data', [UsuariosController::class, 'getUsers'])->name('data');
        Route::post('/update-status', [UsuariosController::class, 'updateStatus'])->name('update-status');
        Route::post('/', [UsuariosController::class, 'store'])->name('store');
        Route::post('/{id}/reenviar-correo', [UsuariosController::class, 'reenviarCorreo'])->name('reenviar-correo');
    });
});


//rutas para retenciones_sri
Route::middleware(['auth'])->group(function () {
    Route::prefix('retenciones-sri')->name('retenciones.')->middleware('can:gestionar-retenciones')->group(function () {
        Route::get('/', [RetencionSriController::class, 'index'])->name('index');
        Route::get('/data', [RetencionSriController::class, 'getData'])->name('data');
        Route::post('/store', [RetencionSriController::class, 'store'])->name('store');
        Route::put('/update/{id}', [RetencionSriController::class, 'update'])->name('update');
        Route::get('/{id}', [RetencionSriController::class, 'show'])->name('show'); // ponerla al final
    });
});

// >>> CategoriaModule START
//permiso admin user: gestionar-categorias
//ruta para submenus: categorias.categorias
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-categorias'])
    ->prefix('empresa/categorias')
    ->name('categorias.')
    ->group(function () {
        Route::get('/buscar', [CategoriaController::class, 'buscar'])->name('buscar'); // opcional si implementas buscar()
        Route::get('/', [CategoriaController::class, 'index'])->name('categorias');
        Route::get('/data', [CategoriaController::class, 'getData'])->name('data');
        Route::post('/crear', [CategoriaController::class, 'create'])->name('create');
        Route::post('/', [CategoriaController::class, 'store'])->name('store');
        Route::get('/{categoria}/show', [CategoriaController::class, 'show'])->name('show');
        Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('update');
        Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('destroy');
    });
// <<< CategoriaModule END

//permiso admin user: gestionar-lista-precios
//ruta para submenus: lista-precios.lista-precios
Route::middleware(['auth', 'verificar.permisos.submenu', 'can:gestionar-lista-precios'])
    ->prefix('empresa/lista-precios')
    ->name('lista-precios.')
    ->group(function () {
        Route::get('/buscar', [ListaPreciosController::class, 'buscar'])->name('buscar'); // opcional si implementas buscar()
        Route::get('/', [ListaPreciosController::class, 'index'])->name('lista-precios'); //este name es el nombre de la ruta ej: lista-precios.lista-precios
        Route::get('/data', [ListaPreciosController::class, 'getData'])->name('data');
        Route::post('/crear', [ListaPreciosController::class, 'create'])->name('create'); // si usas modal
        Route::post('/', [ListaPreciosController::class, 'store'])->name('store');
        Route::get('/{listaPrecio}/show', [ListaPreciosController::class, 'show'])->name('show');
        Route::put('/{listaPrecio}', [ListaPreciosController::class, 'update'])->name('update');
        Route::delete('/{listaPrecio}', [ListaPreciosController::class, 'destroy'])->name('destroy');
    });
