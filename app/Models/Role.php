<?php

namespace App\Models;

use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

/**
 * Merchant Role model.
 *
 * PR-08: Extend Spatie Permission 6 Role instead of re-implementing the
 * Permission 5-era contract (static PermissionRegistrar::$pivotRole etc.).
 * Preserves merchant-specific user_id scoping and soft create() behavior
 * (duplicate name does not throw RoleAlreadyExists).
 */
class Role extends SpatieRole
{
    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

    /**
     * Preserve legacy Merchant create semantics: do not throw on duplicate name.
     *
     * @return static
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];
        $registrar = app(PermissionRegistrar::class);

        if ($registrar->teams) {
            $teamsKey = $registrar->teamsKey;
            if (array_key_exists($teamsKey, $attributes)) {
                $params[$teamsKey] = $attributes[$teamsKey];
            } else {
                $attributes[$teamsKey] = getPermissionsTeamId();
            }
        }

        return static::query()->create($attributes);
    }
}
