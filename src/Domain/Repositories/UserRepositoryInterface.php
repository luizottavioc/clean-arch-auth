<?php 

declare(strict_types=1);

namespace App\Domain\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
}
