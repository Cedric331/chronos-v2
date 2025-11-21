<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getUsersByTeam(int $teamId, array $with = []): Collection
    {
        $query = User::where('team_id', $teamId);

        if (! empty($with)) {
            $query->with($with);
        }

        return $query->get();
    }

    public function getUsersByRole(string $role, array $with = []): Collection
    {
        $query = User::whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });

        if (! empty($with)) {
            $query->with($with);
        }

        return $query->get();
    }

    public function getUsersByRoles(array $roles, array $with = []): Collection
    {
        $query = User::whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });

        if (! empty($with)) {
            $query->with($with);
        }

        return $query->get();
    }

    public function getCoordinateursByCompany(int $companyId): Collection
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'Coordinateur');
        })
            ->where('company_id', $companyId)
            ->get();
    }

    public function getAdmins(): Collection
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'Administrateur');
        })->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
