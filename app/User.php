<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    //このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    //このユーザがお気に入りしてるmicropost。
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    // $userIdで指定されたユーザをフォローする。
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    //$userIdで指定されたユーザをアンフォローする。
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    // 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    // このユーザに関係するモデルの件数をロードする。
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
    }
    
    // このユーザとフォロー中ユーザの投稿に絞り込む
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    // お気に入りに登録する
    public function favorite($micropostId){
        // すでにお気に入り登録しているかの確認
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // すでにお気に入り登録していれば何もしない
            return false;
        } else {
            // まだお気に入りしていなかったら登録する
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    // お気に入りから外す
    public function unfavorite($micropostId){
        // すでにお気に入り登録しているかの確認
        $exist = $this->is_favorite($micropostId);
    
        if ($exist) {
            // すでにお気に入り登録していればお気に入りから外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // まだお気に入り登録していなければ何もしない
            return false;
        }
    }
 
    public function is_favorite($micropostId)
    {
    return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
}
