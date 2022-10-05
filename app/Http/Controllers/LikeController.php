<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\like;

class LikeController extends Controller
{
    public function store(Request $request, $id)
    {
        $like = new Like;
        $meal = Meal::find($id);
        $like->meal_id = $meal->id;
        $like->user_id = $request->user()->id;
        $like->save();
        return back();
    }
    public function destroy(Request $request, $id)
    {
        $like = new Like;
        $meal = Meal::find($id);
        $user = $request->user()->id;
        $like = Like::where('meal_id', $meal->id)
            ->where('user_id', $user)
            ->first();
        $like->delete();
        return back();
    }
}
