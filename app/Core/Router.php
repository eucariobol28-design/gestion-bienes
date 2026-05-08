<?php
declare(strict_types=1); // Activa el modo estricto de tipos para consistencia en tipos de datos.

namespace App\Core; // Define el namespace para la clase Router en el directorio Core.

final class Router // Define la clase Router como final (no heredable), responsable de enrutar URLs a controladores.
{
    /** @var array<int, array<string, mixed>> Rutas definidas manualmente en config/routes.php */
    private array $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        $routesFile = __DIR__ . '/../Config/routes.php';
        if (!is_file($routesFile)) {
            return;
        }

        $routes = require $routesFile;
        if (is_array($routes)) {
            $this->routes = $routes;
        }
    }
    public function dispatch(): void // Método público para despachar la solicitud HTTP a un controlador y acción.
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'; // Obtiene la ruta de la URL desde $_SERVER, por defecto '/'.
        $uri = rtrim($uri, '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $routeInfo = $this->matchRoute($uri, $method);
        if ($routeInfo !== null) {
            $this->dispatchRoute($routeInfo);
            return;
        }

        $uri = trim($uri, '/'); // Elimina barras diagonales al inicio y final para normalizar.
        $parts = $uri === '' ? [] : explode('/', $uri); // Divide la URI en partes usando '/', o array vacío si está vacía.

        $controllerName = $parts[0] ?? 'dashboard'; // Toma la primera parte como nombre del controlador, por defecto 'dashboard'.
        $actionName = $parts[1] ?? 'index'; // Toma la segunda parte como nombre de la acción, por defecto 'index'.

        // Mapeo de rutas especiales si es necesario, pero por ahora simple.
        // Para rutas como /clasificacion, /users, /reportes, usar el mismo patrón.

        // Soportar rutas con prefijo: /controller/action
        // si la URL es /bienes, interpretamos: controller=bienes, action=index
        $controllerName = $controllerName === '' ? 'dashboard' : $controllerName; // Asegura que el controlador no esté vacío.
        $controllerClass = 'App\\Controllers\\' . $this->studly($controllerName) . 'Controller'; // Construye el nombre completo de la clase del controlador usando studly case.

        if (!class_exists($controllerClass)) { // Verifica si la clase del controlador existe.
            $this->notFound(); // Si no existe, llama al método notFound para manejar el error 404.
            return; // Sale del método.
        }

        $controller = new $controllerClass(); // Instancia el controlador dinámicamente.

        if (!method_exists($controller, $actionName)) { // Verifica si el método (acción) existe en el controlador.
            $this->notFound(); // Si no existe, maneja como 404.
        }

        $controller->{$actionName}(); // Llama dinámicamente al método del controlador (ej. index()).
    }

    private function dispatchRoute(array $routeInfo): void
    {
        $route = $routeInfo['route'];
        $params = $routeInfo['params'] ?? [];

        if (isset($route['view']) && is_string($route['view'])) {
            $this->renderView($route['view'], $route['data'] ?? []);
            return;
        }

        if (!isset($route['controller']) || !is_string($route['controller'])) {
            $this->notFound();
            return;
        }

        [$controllerName, $actionName] = explode('@', $route['controller'], 2) + [null, null];
        if ($controllerName === null || $actionName === null) {
            $this->notFound();
            return;
        }

        // Permitir que en `routes.php` el nombre del controlador venga con o sin el sufijo
        // "Controller", o incluso un nombre de clase totalmente cualificado.
        if (str_contains($controllerName, '\\')) {
            // Si ya contiene namespace, asumimos clase completa.
            $controllerClass = $controllerName;
        } elseif (str_ends_with($controllerName, 'Controller')) {
            // Si ya incluye 'Controller', sólo anteponer el namespace App\\Controllers\\
            $controllerClass = 'App\\Controllers\\' . $controllerName;
        } else {
            // Caso habitual: nombre corto (ej. 'dashboard') -> Studly + 'Controller'
            $controllerClass = 'App\\Controllers\\' . $this->studly($controllerName) . 'Controller';
        }

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $actionName)) {
            $this->notFound();
            return;
        }

        if (!empty($params)) {
            $controller->{$actionName}(...array_values($params));
            return;
        }

        $controller->{$actionName}();
    }

    private function matchRoute(string $uri, string $method): ?array
    {
        foreach ($this->routes as $route) {
            if (!isset($route['method'], $route['uri'])) {
                continue;
            }

            if (strtoupper($route['method']) !== strtoupper($method)) {
                continue;
            }

            $pattern = $this->compileRoutePattern($route['uri']);
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    static fn ($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );

                return ['route' => $route, 'params' => $params];
            }
        }

        return null;
    }

    private function compileRoutePattern(string $uri): string
    {
        $uri = rtrim($uri, '/') ?: '/';
        $pattern = preg_replace_callback(
            '/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}/',
            static fn ($matches) => '(?P<' . $matches[1] . '>[^/]+)',
            preg_quote($uri, '#')
        );

        return '#^' . $pattern . '$#';
    }

    private function renderView(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/layouts/header.php';
        require __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../Views/layouts/footer.php';
    }

    private function studly(string $value): string // Método privado para convertir strings a StudlyCase (ej. 'bien' -> 'Bien').
    {
        $value = str_replace(['-', '_'], ' ', $value); // Reemplaza guiones y underscores con espacios.
        $value = ucwords($value); // Convierte la primera letra de cada palabra a mayúscula.
        return str_replace(' ', '', $value); // Elimina espacios para formar StudlyCase.
    }

    private function notFound(): void // Método privado para manejar respuestas 404 (página no encontrada).
    {
        http_response_code(404); // Establece el código HTTP 404.
        // Si existe vista 404, mostrarla.
        $file = __DIR__ . '/../Views/layouts/404.php'; // Ruta al archivo de vista 404.
        if (is_file($file)) { // Si el archivo existe.
            require $file; // Incluye y muestra la vista 404.
            return; // Sale del método.
        }
        echo '404 - Página no encontrada'; // Si no hay vista, muestra un mensaje simple.
    }
}


