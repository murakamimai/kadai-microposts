<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    //投稿をお気に入り登録するアクション。
    public function store($micropostId)
    {
        // 認証済みユーザ（閲覧者）が、 投稿をお気に入り登録する
        (\Auth::user()->favorite($micropostId));
        // 前のURLへリダイレクトさせる
        return back();
    }
 
    //投稿のお気に入り登録を外すアクション。
    public function destroy($micropostId)
    {
        // 認証済みユーザ（閲覧者）が、 投稿をお気に入りから外す
        \Auth::user()->unfavorite($micropostId);
        // 前のURLへリダイレクトさせる
        return back();
    }
    
    public function is_favorite($micropostId)
     {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
     }
}
