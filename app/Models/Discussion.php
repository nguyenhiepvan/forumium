<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'content', 'user_id', 'is_resolved', 'is_public', 'is_locked',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'discussion_tags', 'discussion_id', 'tag_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'source');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'source');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class, 'discussion_id', 'id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'discussion_id', 'user_id')->withPivot('type');
    }

    public function discussionVisits(): HasMany
    {
        return $this->hasMany(DiscussionVisit::class, 'discussion_id', 'id');
    }

    public function updateVisits(): void
    {
        $this->visits = $this->discussionVisits()->count();
        $this->unique_visits = $this->discussionVisits()->groupBy('user_id')->select('user_id')->get()->count();
        $this->save();
    }
}
