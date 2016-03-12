<?php

namespace Proto\Models;

use Zizaco\Entrust\EntrustRole;


/**
 * Proto\Models\Role
 *
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Proto\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\Proto\Models\Permission[] $perms
 */
class Role extends EntrustRole
{
    protected $fillable = ['name', 'display', 'description'];
}
