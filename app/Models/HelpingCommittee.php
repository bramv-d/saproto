<?php

namespace Proto\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Helping Committee Model
 *
 * @property int $id
 * @property int $activity_id
 * @property int $committee_id
 * @property int $amount
 * @property int $notification_sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Activity $activity
 * @property-read Committee $committee
 * @property-read Collection|User[] $users
 * @method static Builder|HelpingCommittee whereActivityId($value)
 * @method static Builder|HelpingCommittee whereAmount($value)
 * @method static Builder|HelpingCommittee whereCommitteeId($value)
 * @method static Builder|HelpingCommittee whereCreatedAt($value)
 * @method static Builder|HelpingCommittee whereId($value)
 * @method static Builder|HelpingCommittee whereNotificationSent($value)
 * @method static Builder|HelpingCommittee whereUpdatedAt($value)
 * @mixin Eloquent
 */
class HelpingCommittee extends Validatable
{
    protected $table = 'committees_activities';

    protected $guarded = ['id'];

    protected $rules = [
        'activity_id' => 'required|integer',
        'committee_id' => 'required|integer',
        'amount' => 'required|integer'
    ];

    /** @return BelongsTo|Activity */
    public function activity()
    {
        return $this->belongsTo('Proto\Models\Activity');
    }

    /** @return BelongsTo|Committee */
    public function committee()
    {
        return $this->belongsTo('Proto\Models\Committee');
    }

    /** @return BelongsToMany|User[] */
    public function users()
    {
        return $this
            ->belongsToMany('Proto\Models\User', 'activities_users', 'committees_activities_id')
            ->whereNull('activities_users.deleted_at')
            ->withTrashed();
    }

    /** @return int */
    public function getHelpingCount()
    {
        return ActivityParticipation::where('activity_id', $this->activity->id)->where('committees_activities_id', $this->id)->count();
    }
}
