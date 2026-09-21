<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibility
{
    public function scopeVisibleTo(Builder $query, ?User $viewer): Builder
    {
        return $query->where(function (Builder $q) use ($viewer) {
            $q->where('visibility', 'public');

            if ($viewer) {
                $q->orWhere('admin_id', $viewer->id);

                $q->orWhere(function (Builder $q2) use ($viewer) {
                    $q2->where('visibility', 'member')
                        ->whereIn('admin_id', $viewer->admins()->select('users.id'));
                });
            }
        });
    }
}
