<?php

namespace App\Models\Concerns;

use Illuminate\Validation\ValidationException;

trait GuardsNaturalKeyUniqueness
{
    public function ensureUniqueNaturalKey(
        array $columns,
        string $errorKey,
        string $message,
    ): void {
        $query = static::query();

        foreach ($columns as $column) {
            $query->where($column, $this->getAttribute($column));
        }

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                $errorKey => [$message],
            ]);
        }
    }
}
