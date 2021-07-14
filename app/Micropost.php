<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];

    // この投稿を所有するユーザ。（ Userモデルとの関係を定義）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // このお気に入りされているmicropostを所有するユーザ。（ Userモデルとの関係を定義）
    public function favorite_users()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }
}