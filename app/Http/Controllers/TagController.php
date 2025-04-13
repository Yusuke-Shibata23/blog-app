<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * タグリストを取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(config('const.tags'));
    }
} 