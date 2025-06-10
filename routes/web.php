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

//para registrar los datos del nuevo usuario cuando le envian el link desde crear usuarios
Route::get('completar-registro/{token}', [RegistroController::class, 'mostrarFormulario'])->name('completar-registro.formulario');
Route::post('completar-registro/{token}', [RegistroController::class, 'guardarDatos'])->name('completar-registro.guardar');


// routes/web.php

//rutas para asignar usuarios a usuarios admin
Route::middleware(['auth'])
    ->prefix('usuario-asignado')
    ->name('usuario_asignado.')
    ->middleware('can:gestionar-usuario-asignado')
    ->group(function () {
        // Muestra la vista principal con la tabla
        Route::get('/', [UsuarioAsignadoController::class, 'index'])->name('index');
        // Endpoint para DataTables (server-side)
        Route::get('/data', [UsuarioAsignadoController::class, 'getData'])->name('data');
        // Asignar un usuario al admin logueado
        Route::post('/asignar', [UsuarioAsignadoController::class, 'store'])->name('store');
        // Quitar la asignación
        Route::delete('/eliminar/{id}', [UsuarioAsignadoController::class, 'destroy'])->name('eliminar');
    });


//para asignar establecimientos a usuarios administrados por admin
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
        // Crear
        Route::post('/store', [EstablecimientoController::class, 'store'])->name('store');
        // Editar (traer datos vía AJAX)
        Route::get('/edit/{id}', [EstablecimientoController::class, 'edit'])->name('edit');
        // Actualizar (POST con _method=PUT desde AJAX)
        Route::put('/update/{establecimiento}', [EstablecimientoController::class, 'update'])->name('update');
        // Mostrar (debe estar al final para evitar conflicto con /edit/{id})
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
Route::middleware(['auth'])->group(function () {
    Route::prefix('usuarios')->name('usuarios.')->middleware('can:gestionar-usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('index');
        Route::get('/data', [UsuariosController::class, 'getUsers'])->name('data');
        Route::post('/update-status', [UsuariosController::class, 'updateStatus'])->name('update-status');
        Route::post('/', [UsuariosController::class, 'store'])->name('store');
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
