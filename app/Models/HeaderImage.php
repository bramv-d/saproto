<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Header Image Model.
 *
 * @property int $id
 * @property string $title
 * @property int|null $credit_id
 * @property int $image_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read StorageEntry $image
 * @property-read User|null $user
 *
 * @method static Builder|HeaderImage whereCreatedAt($value)
 * @method static Builder|HeaderImage whereCreditId($value)
 * @method static Builder|HeaderImage whereId($value)
 * @method static Builder|HeaderImage whereImageId($value)
 * @method static Builder|HeaderImage whereTitle($value)
 * @method static Builder|HeaderImage whereUpdatedAt($value)
 * @method static Builder|HeaderImage newModelQuery()
 * @method static Builder|HeaderImage newQuery()
 * @method static Builder|HeaderImage query()
 *
 * @mixin Eloquent
 */
class HeaderImage extends Model
{
    protected $table = 'headerimages';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'credit_id');
    }

    /** @return BelongsTo */
    public function image()
    {
        return $this->belongsTo('App\Models\StorageEntry', 'image_id', 'id');
    }
}
