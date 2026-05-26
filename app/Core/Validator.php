<?php
declare(strict_types=1); // Modo estricto.

namespace App\Core; // Namespace en Core.

final class Validator // Clase final para validar datos de formularios con reglas personalizables.
{
    private array $errors = []; // Array privado para almacenar errores de validación.

    public function validate(array $data, array $rules): bool // Valida datos contra reglas; retorna true si pasa.
    {
        $this->errors = []; // Resetea errores.

        foreach ($rules as $field => $fieldRules) { // Itera sobre cada campo y sus reglas.
            $value = $data[$field] ?? null; // Obtiene valor del campo, o null si no existe.

            foreach ((array)$fieldRules as $rule) { // Itera reglas (pueden ser array o string).
                if (!$this->checkRule($field, $value, $rule)) { // Verifica regla.
                    break; // Para en el primer error por campo.
                }
            }
        }

        return empty($this->errors); // Retorna true si no hay errores.
    }

    public function errors(): array // Retorna array de errores.
    {
        return $this->errors; // Ej. ['campo' => ['mensaje1', 'mensaje2']].
    }

    private function checkRule(string $field, $value, string $rule): bool // Verifica una regla específica.
    {
        $parts = explode(':', $rule, 2); // Separa nombre de regla y parámetro (ej. 'max:255').
        $ruleName = $parts[0];
        $param = $parts[1] ?? null; // Parámetro o null.

        switch ($ruleName) { // Evalúa el tipo de regla.
            case 'required': // Campo obligatorio.
                if (empty($value) && $value !== '0') { // Vacío pero no '0'.
                    $this->addError($field, 'El campo es requerido'); // Agrega error.
                    return false; // Falla.
                }
                break;
            case 'string': // Debe ser string.
                if (!is_string($value)) {
                    $this->addError($field, 'Debe ser una cadena de texto');
                    return false;
                }
                break;
            case 'max': // Longitud máxima.
                if (is_string($value) && strlen($value) > (int)$param) { // Si excede.
                    $this->addError($field, "No puede exceder {$param} caracteres");
                    return false;
                }
                break;
            case 'in': // Valor en lista.
                $options = explode(',', $param); // Separa opciones por coma.
                if (!in_array($value, $options, true)) { // Si no está en opciones.
                    $this->addError($field, 'Valor no válido');
                    return false;
                }
                break;
            case 'unique': // Valor único en DB.
                // Para unique, asumimos tabla:campo
                [$table, $column] = explode(':', $param); // Separa tabla y columna.
                if ($this->existsInDb($table, $column, $value)) { // Si existe en DB.
                    $this->addError($field, 'Ya existe un registro con este valor');
                    return false;
                }
                break;
        }

        return true; // Regla pasa.
    }

    private function addError(string $field, string $message): void // Agrega error a un campo.
    {
        $this->errors[$field][] = $message; // Array de mensajes por campo.
    }

    private function existsInDb(string $table, string $column, $value): bool // Verifica si valor existe en DB.
    {
        $config = require __DIR__ . '/../Config/config.php'; // Carga config.
        $db = new Database($config['db']); // Instancia DB.
        $pdo = $db->pdo(); // Obtiene PDO.

        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$column} = :value LIMIT 1"); // Query preparada.
        $stmt->execute([':value' => $value]); // Ejecuta con valor.

        return (bool)$stmt->fetch(); // Retorna true si existe.
    }
}