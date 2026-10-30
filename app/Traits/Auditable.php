<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot trait Auditable dan pasang hook lifecycle Eloquent.
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            static::logAuditActivity('created', $model, null, static::filterAuditableAttributes($model->getAttributes()));
        });

        static::updated(function (Model $model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            $newValues = [];

            foreach ($dirty as $key => $newValue) {
                if (static::isIgnoredAuditAttribute($key)) {
                    continue;
                }

                $oldValue = $model->getOriginal($key);

                if (static::valuesAreEqual($oldValue, $newValue)) {
                    continue;
                }

                $oldValues[$key] = $oldValue;
                $newValues[$key] = $newValue;
            }

            if (!empty($newValues)) {
                static::logAuditActivity('updated', $model, $oldValues, $newValues);
            }
        });

        static::deleted(function (Model $model) {
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            static::logAuditActivity($action, $model, static::filterAuditableAttributes($model->getOriginal()), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                static::logAuditActivity('restored', $model, null, static::filterAuditableAttributes($model->getAttributes()));
            });
        }
    }

    protected static function logAuditActivity(string $action, Model $model, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::record(
            action: $action,
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    protected static function isIgnoredAuditAttribute(string $key): bool
    {
        $ignored = [
            'updated_at',
            'created_at',
            'deleted_at',
            'remember_token',
            'password',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];

        return in_array($key, $ignored, true);
    }

    protected static function filterAuditableAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $val) {
            if (static::isIgnoredAuditAttribute($key)) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    protected static function valuesAreEqual(mixed $val1, mixed $val2): bool
    {
        if ($val1 === $val2) {
            return true;
        }

        if (is_array($val1) || is_array($val2)) {
            return json_encode($val1) === json_encode($val2);
        }

        return (string) $val1 === (string) $val2;
    }
}

