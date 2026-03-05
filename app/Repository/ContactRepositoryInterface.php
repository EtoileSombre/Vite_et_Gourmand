<?php

namespace App\Repository;

interface ContactRepositoryInterface
{
    public function createContact(array $data): int;
    public function findAllContacts(?string $statut = null): array;

    //Compte les messages par statut
    public function countByStatut(string $statut): int;
    public function updateStatut(int $id, string $statut): bool;
    public function delete(int $id): bool;
}