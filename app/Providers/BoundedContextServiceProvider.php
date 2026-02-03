<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Src\Auth\Domain\Contracts\UserRepositoryInterface;
use Src\Auth\Infrastructure\Repositories\EloquentUserRepository;

class BoundedContextServiceProvider extends ServiceProvider
{
    /**
     * Lista de bounded contexts del sistema
     * Orden importante: las dependencias deben ir primero
     */
    protected array $boundedContexts = [
        // Módulos base (sin dependencias)
        'Auth',
        'Cliente',
        'Categoria',
        'Producto',

        // Módulos del Sistema de Reparación de Celulares
        'Tecnico',           // Técnicos de reparación
        'Equipo',            // Equipos/celulares de clientes
        'Repuesto',          // Inventario de repuestos
        'OrdenReparacion',   // Órdenes de reparación
        'DetalleOrdenRepuesto', // Repuestos usados en órdenes
        'Factura',           // Facturación (modificado para reparaciones)
        'ConsultaEstado',    // Consulta pública de estado
    ];

    /**
     * Bounded contexts con rutas públicas (sin autenticación)
     */
    protected array $publicContexts = [
        'ConsultaEstado',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Registro de bindings para Auth (único módulo con DDD completo)
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadBoundedContextRoutes();
        $this->loadBoundedContextMigrations();
    }

    /**
     * Cargar las rutas de cada bounded context
     */
    protected function loadBoundedContextRoutes(): void
    {
        foreach ($this->boundedContexts as $context) {
            // Cargar rutas de API
            $apiRoutesPath = base_path("src/{$context}/api.php");
            if (file_exists($apiRoutesPath)) {
                Route::prefix('api/v1')
                    ->middleware('api')
                    ->group($apiRoutesPath);
            }

            // Cargar rutas Web
            $webRoutesPath = base_path("src/{$context}/web.php");
            if (file_exists($webRoutesPath)) {
                Route::middleware('web')
                    ->group($webRoutesPath);
            }
        }
    }

    /**
     * Cargar las migraciones de cada bounded context
     */
    protected function loadBoundedContextMigrations(): void
    {
        foreach ($this->boundedContexts as $context) {
            $migrationsPath = base_path("src/{$context}/Infrastructure/Migrations");

            if (is_dir($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }
}
