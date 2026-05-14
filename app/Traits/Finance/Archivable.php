<?php

namespace App\Traits\Finance;

use Illuminate\Database\Eloquent\Model;

trait Archivable
{
    public function scopeArchived($query)
    {
        return $query->onlyTrashed();
    }

    public function scopeWithArchived($query)
    {
        return $query->withTrashed();
    }

    public function scopeWithoutArchived($query)
    {
        return $query->whereNull($this->getQualifiedDeletedAtColumn());
    }

    public function archive(): bool
    {
        if (! $this instanceof \Illuminate\Database\Eloquent\SoftDeletes) {
            return false;
        }

        $this->fill([
            'status' => false,
            'is_archived' => true,
        ]);

        $this->save();

        return $this->delete();
    }

    public function restoreArchive(): bool
    {
        if (! $this instanceof \Illuminate\Database\Eloquent\SoftDeletes) {
            return false;
        }

        $this->restore();

        $this->fill([
            'status' => true,
            'is_archived' => false,
        ]);

        return $this->save();
    }

    public function isArchived(): bool
    {
        return $this->trashed() || (bool) ($this->is_archived ?? false);
    }

    public function getArchiveLabelAttribute(): string
    {
        if ($this->trashed() || ($this->is_archived ?? false)) {
            return 'Archived';
        }

        return $this->status ? 'Active' : 'Inactive';
    }
}
