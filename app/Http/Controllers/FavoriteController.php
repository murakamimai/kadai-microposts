<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
    * 投稿をお気に入り登録するアクション。
    *
    * @param $id 相手ユーザのid
    * @return \Illuminate\Http\Response
    */
    public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、 投稿をお気に入り登録する
        \Auth::user()->favorite($microsoftId);
        // 前のURLへリダイレクトさせる
        return back();
    }
 
    /**
    * 投稿のお気に入り登録を外すアクション。
    *
    * @param $id 相手ユーザのid
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        // 認証済みユーザ（閲覧者）が、 idのユーザをアンフォローする
        \Auth::user()->unfollow($id);
        // 前のURLへリダイレクトさせる
        return back();
    }
}
