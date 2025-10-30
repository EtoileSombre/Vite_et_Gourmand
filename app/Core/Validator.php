<?php

namespace App\Core;

/**
 * Classe Validator
 * Valide les données des formulaires
 */
class Validator
{
    private array $errors = [];

    /**
     * Valide un ensemble de données selon des règles
     * 
     * @param array $data Données à valider
     * @param array $rules Règles de validation
     * @return bool True si valide, False sinon
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $ruleset);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Applique une règle de validation
     * 
     * @param string $field Nom du champ
     * @param mixed $value Valeur du champ
     * @param string $rule Règle à appliquer
     * @return void
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Required
        if ($rule === 'required' && empty($value)) {
            $this->errors[$field][] = "Le champ $field est requis";
            return;
        }

        // Email
        if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Le champ $field doit être un email valide";
        }

        // Min length
        if (str_starts_with($rule, 'min:')) {
            $min = (int)substr($rule, 4);
            if (!empty($value) && strlen($value) < $min) {
                $this->errors[$field][] = "Le champ $field doit contenir au moins $min caractères";
            }
        }

        // Max length
        if (str_starts_with($rule, 'max:')) {
            $max = (int)substr($rule, 4);
            if (!empty($value) && strlen($value) > $max) {
                $this->errors[$field][] = "Le champ $field ne doit pas dépasser $max caractères";
            }
        }

        // Numeric
        if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = "Le champ $field doit être numérique";
        }

        // Integer
        if ($rule === 'integer' && !empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = "Le champ $field doit être un entier";
        }
    }

    /**
     * Récupère les erreurs de validation
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Récupère les erreurs pour un champ spécifique
     * 
     * @param string $field Nom du champ
     * @return array
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Vérifie si un champ a des erreurs
     * 
     * @param string $field Nom du champ
     * @return bool
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}
