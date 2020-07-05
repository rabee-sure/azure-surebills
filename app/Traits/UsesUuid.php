<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UsesUuid
{
    protected static function bootUsesUuid()
    {
        static::creating(function ($model) {
            $uuid = Uuid::uuid4();
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = $uuid->toString();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}