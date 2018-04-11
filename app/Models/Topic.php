<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeWithOrder($query, $order)
    {
        switch ($order) {
            case 'create':
                $query = $this->recentCreate();
                break;
            default:
                $query = $this->recentReply();
                break;
        }

        return $query->with('user', 'category');
    }

    public function scopeRecentReply($query)
    {
        return $this->orderBy('updated_at', 'desc');
    }

    public function scopeRecentCreate($query)
    {
        return $this->orderBy('created_at', 'desc');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'topic_id', 'id');
    }
}
