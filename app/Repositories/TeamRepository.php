<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    public function find(int $id): ?Team
    {
        return Team::find($id);
    }

    public function findOrFail(int $id): Team
    {
        return Team::findOrFail($id);
    }

    public function getTeamsByCompany(int $companyId, array $with = []): Collection
    {
        $query = Team::where('company_id', $companyId);

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->orderBy('name')->get();
    }

    public function getTeamWithRelations(int $teamId, array $relations = []): ?Team
    {
        $query = Team::where('id', $teamId);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->first();
    }

    public function getTeamWithUsers(int $teamId): ?Team
    {
        return Team::with(['users', 'coordinateur', 'params'])
            ->withCount('users')
            ->find($teamId);
    }

    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(Team $team, array $data): bool
    {
        return $team->update($data);
    }

    public function delete(Team $team): bool
    {
        return $team->delete();
    }
}

