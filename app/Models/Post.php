<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'published_at',
        'scheduled_at',
        'thumbnail_path',
        'user_id',
        'tags'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    protected $appends = [
        'thumbnail_url'
    ];

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the post.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            });
    }

    /**
     * Scope a query to only include draft posts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include scheduled posts.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'published')
            ->where('scheduled_at', '>', now());
    }

    /**
     * Check if the post is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
            ($this->scheduled_at === null || $this->scheduled_at <= now());
    }

    /**
     * Check if the post is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the post is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'published' && 
            $this->scheduled_at !== null && 
            $this->scheduled_at > now();
    }

    /**
     * タグでフィルタリングするスコープ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|array $tagIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTags($query, $tagIds)
    {
        if (empty($tagIds)) {
            return $query;
        }

        $tagIds = is_array($tagIds) ? $tagIds : [$tagIds];

        return $query->where(function ($query) use ($tagIds) {
            foreach ($tagIds as $tagId) {
                $query->orWhereJsonContains('tags', $tagId);
            }
        });
    }

    /**
     * キーワードで検索するスコープ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
        });
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        
        // デフォルトサムネイルのURLを返す
        return 'https://images.unsplash.com/photo-1498050108023-c5249f4df085';
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * タグ名の配列を取得
     *
     * @return array
     */
    public function getTagNamesAttribute()
    {
        if (empty($this->tags)) {
            return [];
        }

        $tagList = config('const.tags');
        return array_map(function ($tagId) use ($tagList) {
            return $tagList[$tagId] ?? null;
        }, $this->tags);
    }

    /**
     * タグを取得する
     *
     * @param string $value
     * @return array
     */
    public function getTagsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        return json_decode($value, true) ?? [];
    }

    /**
     * タグを保存する
     *
     * @param array|string $value
     * @return void
     */
    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['tags'] = $value;
        }
    }
}
