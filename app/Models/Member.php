<?php

namespace Proto\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'members';

    protected $guarded = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('Proto\Models\User')->withTrashed;
    }
}
