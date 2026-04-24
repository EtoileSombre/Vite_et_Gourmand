<?php

namespace App\Services;

use App\Services\Exceptions\DomainException;

/**
 * Classe de base pour les services métier.
 */
abstract class AbstractService
{
    /**
     * @param array<string, mixed> $data
     * @param string[] $fields
     * @throws DomainException si un champ est absent ou vide
     */
    protected function requireFields(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new DomainException("Champ obligatoire manquant : {$field}");
            }
            $value = $data[$field];
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                throw new DomainException("Champ obligatoire vide : {$field}");
            }
        }
    }

    /** Trim et renvoie null si la chaîne est vide. */
    protected function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }
}
