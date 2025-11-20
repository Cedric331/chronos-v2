<?php

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository
{
    public function find(int $id): ?Ticket
    {
        return Ticket::find($id);
    }

    public function findOrFail(int $id): Ticket
    {
        return Ticket::findOrFail($id);
    }

    public function getTicketsForTeam(int $teamId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Ticket::with(['status', 'category', 'priority', 'creator', 'assignee'])
            ->where('team_id', $teamId);

        // Appliquer les filtres
        if (isset($filters['status_id']) && $filters['status_id']) {
            $query->where('status_id', $filters['status_id']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['priority_id']) && $filters['priority_id']) {
            $query->where('priority_id', $filters['priority_id']);
        }

        if (isset($filters['assigned_to']) && $filters['assigned_to']) {
            if ($filters['assigned_to'] === 'me') {
                $query->where('assigned_to', $filters['user_id']);
            } else {
                $query->where('assigned_to', $filters['assigned_to']);
            }
        }

        if (isset($filters['created_by']) && $filters['created_by']) {
            if ($filters['created_by'] === 'me') {
                $query->where('created_by', $filters['user_id']);
            } else {
                $query->where('created_by', $filters['created_by']);
            }
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Tri
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSortFields = ['id', 'title', 'created_at', 'updated_at', 'due_date'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getTicketWithRelations(int $ticketId, int $userId, bool $isAdmin): ?Ticket
    {
        $query = Ticket::with([
            'creator',
            'assignee',
            'status',
            'category',
            'priority',
            'team',
            'tags',
            'comments' => function ($q) use ($isAdmin) {
                if (!$isAdmin) {
                    $q->where('is_internal', false);
                }
                $q->with(['user', 'attachments']);
            },
            'attachments',
            'histories' => function ($q) {
                $q->with('user')->orderBy('created_at', 'desc');
            },
            'subscribers',
        ]);

        return $query->find($ticketId);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(Ticket $ticket, array $data): bool
    {
        return $ticket->update($data);
    }

    public function delete(Ticket $ticket): bool
    {
        return $ticket->delete();
    }

    public function getOpenTicketsForTeam(int $teamId): Collection
    {
        return Ticket::where('team_id', $teamId)
            ->open()
            ->get();
    }

    public function getOverdueTicketsForTeam(int $teamId): Collection
    {
        return Ticket::where('team_id', $teamId)
            ->overdue()
            ->get();
    }
}

