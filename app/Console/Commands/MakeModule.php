<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module
        {name : Nombre del módulo en singular, ej. Empresa}
        {--plural= : Plural kebab para rutas y vistas, ej. empresas}
        {--fields= : Campos para migración/validación/DT, ej: "ruc:string(13):index, razon_social:string(200):index, email:string(150):nullable, estado:boolean"}
        {--table= : Nombre de la tabla (por defecto plural snake del nombre)}
        {--force : Sobrescribir archivos si existen}';

    protected $description = 'Genera Modelo, Controlador (Empresa), Migración, Vistas (index + modal + tab general) y Rutas con can:gestionar-{plural}.';

    public function handle()
    {
        $fs = new Filesystem();

        $name        = Str::studly($this->argument('name'));                       // Empresa
        $moduleLower = Str::camel($name);                                          // empresa
        $plural      = $this->option('plural') ?: Str::kebab(Str::pluralStudly($name)); // empresas
        $table       = $this->option('table') ?: Str::snake(Str::pluralStudly($name));  // empresas
        $fieldsRaw   = (string) $this->option('fields');
        $force       = (bool) $this->option('force');

        // Parseo de fields → piezas para stubs
        [$fillable, $migrationCols, $validationRules, $headers, $filters, $dtColumns, $mapColumns, $formFields, $fillFormForEdit]
            = $this->parseFields($fieldsRaw);

        // Destinos
        $pluralStudly = Str::pluralStudly($name); // Categorias
        $modelPath = base_path("app/Models/Empresa/{$pluralStudly}/{$name}.php");
        $ctrlPath    = base_path("app/Http/Controllers/Empresa/{$name}Controller.php");
        $request    = base_path("app/Http/Requests/{$name}Request.php");
        $viewsDir    = base_path("resources/views/empresa/{$plural}");
        $partialsDir = "{$viewsDir}/partials";
        $tabsDir     = "{$viewsDir}/tabs";
        $indexPath   = "{$viewsDir}/index.blade.php";
        $modalPath   = "{$partialsDir}/modal_{$moduleLower}.blade.php";
        $tabsGenPath = "{$tabsDir}/general.blade.php";

        // Asegurar carpetas
        $fs->ensureDirectoryExists(dirname($modelPath));
        $fs->ensureDirectoryExists(dirname($ctrlPath));
        $fs->ensureDirectoryExists(dirname($request));
        $fs->ensureDirectoryExists($viewsDir);
        $fs->ensureDirectoryExists($partialsDir);
        $fs->ensureDirectoryExists($tabsDir);

        // Cargar stubs
        $stubDir    = base_path('stubs/module');
        $modelStub  = $this->getStub("{$stubDir}/Model.stub");
        $ctrlStub   = $this->getStub("{$stubDir}/Controller.stub");
        $requestStub   = $this->getStub("{$stubDir}/Request.stub");
        $migStub    = $this->getStub("{$stubDir}/migration.stub");
        $idxStub    = $this->getStub("{$stubDir}/views/index.stub.blade.php");
        $modalStub  = $this->getStub("{$stubDir}/views/partials/modal.stub.blade.php");
        $tabStub    = $this->getStub("{$stubDir}/views/tabs/general.stub.blade.php");
        $routesStub = $this->getStub("{$stubDir}/routes_web.stub");

        // Reemplazos
        $routeParam = '{' . $moduleLower . '}'; // para evitar problemas de interpolación
        $repl = [
            '{{ module }}'          => $name,
            '{{ modulePlural }}'    => Str::pluralStudly($name),
            '{{ moduleLower }}'     => $moduleLower,
            '{{ plural }}'          => $plural,
            '{{ table }}'           => $table,
            '{{ fillable }}'        => $fillable,
            '{{ migrationCols }}'   => $migrationCols,
            '{{ validationRules }}' => $validationRules,
            '{{ tableHeaders }}'    => $headers,
            '{{ filterInputs }}'    => $filters,
            '{{ dtColumns }}'       => $dtColumns,
            '{{ mapColumns }}'      => $mapColumns,
            '{{ formFields }}'      => $formFields,
            '{{ fillFormForEdit }}' => $fillFormForEdit,
            '{{ routeParam }}'      => $routeParam,
        ];

        // Escribir archivos principales
        $this->putFile($modelPath, str_replace(array_keys($repl), array_values($repl), $modelStub), $force);
        $this->putFile($ctrlPath,  str_replace(array_keys($repl), array_values($repl), $ctrlStub),  $force);
        $this->putFile($request,  str_replace(array_keys($repl), array_values($repl), $requestStub),  $force);

        // Vistas
        $this->putFile($indexPath,   str_replace(array_keys($repl), array_values($repl), $idxStub),   $force);
        $this->putFile($modalPath,   str_replace(array_keys($repl), array_values($repl), $modalStub), $force);
        $this->putFile($tabsGenPath, str_replace(array_keys($repl), array_values($repl), $tabStub),   $force);

        // Migración
        $ts = date('Y_m_d_His');
        $migPath = base_path("database/migrations/{$ts}_create_{$table}_table.php");
        $this->putFile($migPath, str_replace(array_keys($repl), array_values($repl), $migStub), $force);

        // Rutas (anexar bloque al final de routes/web.php)
        $this->appendRoutesFromStub($routesStub, $repl);

        $this->info("✅ Módulo {$name} generado. Tabla: {$table}. Rutas: /{$plural}");
        return self::SUCCESS;
    }

    // ---------- Helpers ----------

    protected function getStub(string $path): string
    {
        if (!is_file($path)) {
            $this->error("No se encontró stub: {$path}");
            exit(1);
        }
        return file_get_contents($path);
    }

    protected function putFile(string $path, string $content, bool $force): void
    {
        if (is_file($path) && !$force) {
            $this->warn("Ya existe: {$path} (usa --force para sobrescribir)");
            return;
        }
        (new Filesystem())->put($path, $content);
        $this->line("Creado: {$path}");
    }

    protected function appendRoutesFromStub(string $routesStub, array $repl): void
    {
        $routes = base_path('routes/web.php');
        $blockStart = "// >>> {$repl['{{ module }}']}Module START";
        $blockEnd   = "// <<< {$repl['{{ module }}']}Module END";

        $content = str_replace(array_keys($repl), array_values($repl), $routesStub);
        $content = "\n{$blockStart}\n" . trim($content) . "\n{$blockEnd}\n";

        $file = file_get_contents($routes);
        if (str_contains($file, $blockStart)) {
            $this->warn("Bloque de rutas ya existe para {$repl['{{ module }}']}, no duplico.");
            return;
        }
        file_put_contents($routes, $file . PHP_EOL . $content);
        $this->line("Rutas añadidas a routes/web.php.");
    }

    /**
     * Parsea fields="campo:tipo(args):flags..." → piezas para migración, fillable, validación, DataTables, form y JS de edición.
     */
    protected function parseFields(string $raw): array
    {
        $items = array_filter(array_map('trim', explode(',', $raw ?: '')));
        $fillable = [];
        $mig = [];
        $rules = [];
        $headers = [];
        $filters = [];
        $dtCols = [];
        $map = [];
        $form = [];
        $fillJs = [];

        $idx = 0;
        foreach ($items as $it) {
            // Ej: "ruc:string(13):index" | "email:string(150):nullable" | "status:boolean"
            $parts = array_map('trim', explode(':', $it));
            $col   = $parts[0] ?? null;
            $type  = $parts[1] ?? 'string';
            $mods  = array_slice($parts, 2);

            if (!$col) continue;

            $fillable[] = "'{$col}'";

            // ----- Caso especial: status => enum('Activo','Inactivo') -----
            if ($col === 'estado') {
                // Migración
                $mig[] = "\$table->enum('estado', ['activo','inactivo'])->default('activo')->index();";

                // Validación
                $rules[] = "'estado' => ['required','in:activo,inactivo'],";

                // Filtro columna
                $filters[] = "<th><select class=\"form-control form-control-sm\"><option value=\"\">Todos</option><option value=\"activo\">Activo</option><option value=\"inactivo\">Inactivo</option></select></th>";

                // DataTables (server-side ya formatea con badge si quieres; aquí solo columna)
                $dtCols[]  = "{ data: 'estado', name: 'estado', orderable:false, searchable:false },";

                // Formulario
                $form[] = <<<HTML
<div class="col-md-4 mb-3">
  <label class="form-label">Estado *</label>
  <select name="estado" class="form-select" required>
    <option value="activo">Activo</option>
    <option value="inactivo">Inactivo</option>
  </select>
</div>
HTML;

                // JS para rellenar modal en edición
                $fillJs[] = "$('[name=\"estado\"]').val(data.estado ?? 'activo');";

                // Encabezado y mapa
                $headers[] = "<th>Estado</th>";
                $map[]     = "{$idx} => 'estado',";
                $idx++;
                continue;
            }

            // ----- Migración genérica (no status) -----
            [$baseType, $args] = $this->splitTypeArgs($type); // string(150) -> ['string','150']
            $line = "\$table->{$baseType}('{$col}'" . ($args ? ", {$args}" : "") . ")";
            foreach ($mods as $m) {
                if ($m === 'nullable') $line .= "->nullable()";
                if ($m === 'unique')   $line .= "->unique()";
                if ($m === 'index')    $line .= "->index()";
            }
            $line .= ';';
            $mig[] = $line;

            // ----- Validación + Filtros + DT + Form + JS para el resto -----
            if ($baseType === 'boolean') {
                // Validación boolean
                $rules[] = "'{$col}' => ['required','boolean'],";

                // Filtro select 1/0
                $filters[] = "<th><select class=\"form-control form-control-sm\"><option value=\"\">Todos</option><option value=\"1\">Activo</option><option value=\"0\">Inactivo</option></select></th>";

                // Columna DT (no ordenable/buscable en cliente)
                $dtCols[]  = "{ data: '{$col}', name: '{$col}', orderable:false, searchable:false },";

                // Form (select 1/0)
                $requiredStar = in_array('nullable', $mods) ? '' : ' *';
                $form[] = <<<HTML
<div class="col-md-3 mb-3">
  <label class="form-label">{$this->labelize($col)}{$requiredStar}</label>
  <select name="{$col}" class="form-select">
    <option value="activo">Activo</option>
    <option value="inactivo">Inactivo</option>
  </select>
</div>
HTML;

                // JS edición
                $fillJs[] = "$('[name=\"{$col}\"]').val(String(data.{$col} ?? '1'));";
            } elseif ($col === 'email') {
                // Email
                $rules[]   = "'{$col}' => ['nullable','email','max:150'],";
                $filters[] = "<th><input class=\"form-control form-control-sm\" placeholder=\"Email\"></th>";
                $dtCols[]  = "{ data: '{$col}', name: '{$col}' },";

                $form[]    = <<<HTML
<div class="col-md-4 mb-3">
  <label class="form-label">Email</label>
  <input type="email" name="{$col}" class="form-control" value="{{ old('{$col}', \$item->{$col} ?? '') }}">
</div>
HTML;

                $fillJs[] = "$('[name=\"{$col}\"]').val(data.{$col} ?? '');";
            } else {
                // Strings/otros
                $max         = $this->guessMax($args);
                $required    = in_array('nullable', $mods) ? 'nullable' : 'required';
                $rules[]     = "'{$col}' => ['{$required}','string','max:{$max}'],";

                $filters[]   = "<th><input class=\"form-control form-control-sm\" placeholder=\"" . $this->labelize($col) . "\"></th>";
                $dtCols[]    = "{ data: '{$col}', name: '{$col}' },";

                $requiredStar = in_array('nullable', $mods) ? '' : ' *';
                $form[] = <<<HTML
<div class="col-md-4 mb-3">
  <label class="form-label">{$this->labelize($col)}{$requiredStar}</label>
  <input type="text" name="{$col}" class="form-control" value="{{ old('{$col}', \$item->{$col} ?? '') }}">
</div>
HTML;

                $fillJs[] = "$('[name=\"{$col}\"]').val(data.{$col} ?? '');";
            }

            // Encabezado y mapa
            $headers[] = "<th>" . $this->labelize($col) . "</th>";
            $map[]     = "{$idx} => '{$col}',";
            $idx++;
        }

        // Mínimo si no pasaron fields
        if (empty($items)) {
            $headers[] = "<th>ID</th>";
            $filters[] = "<th><input class=\"form-control form-control-sm\" placeholder=\"ID\"></th>";
            $dtCols[]  = "{ data: 'id', name: 'id' },";
            $map[]     = "0 => 'id',";
            $fillJs[]  = "$('[name=\"id\"]').val(data.id ?? '');";
        }

        return [
            implode(', ', $fillable),                    // fillable
            implode("\n            ", $mig),             // migrationCols
            implode("\n            ", $rules),           // validationRules
            implode("\n          ", $headers),           // tableHeaders
            implode("\n          ", $filters),           // filterInputs
            implode("\n      ", $dtCols),                // dtColumns
            implode("\n            ", $map),             // mapColumns
            implode("\n  ", $form),                      // formFields
            implode("\n      ", $fillJs),                // fillFormForEdit (JS)
        ];
    }


    protected function splitTypeArgs(string $type): array
    {
        if (preg_match('/^([a-z_]+)\((.+)\)$/i', $type, $m)) {
            return [$m[1], $m[2]];
        }
        return [$type, ''];
    }

    protected function guessMax(string $args): int
    {
        if (preg_match('/^\s*(\d+)/', $args, $m)) return (int)$m[1];
        return 150;
    }

    protected function labelize(string $col): string
    {
        return Str::headline($col);
    }
}
