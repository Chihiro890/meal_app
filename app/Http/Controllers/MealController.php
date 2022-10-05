<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealRequest;
use App\Models\Meal;
use App\Models\Like;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $meals = Meal::with('user')->latest()->paginate(4);


        return view('meals.index', compact('meals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        // dd($categories);
        return view('meals.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MealRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MealRequest $request)
    {
        $meal = new Meal($request->all());
        $meal->user_id = $request->user()->id;



        $file = $request->file('image');
        $meal->image = self::createFileName($file);
        // $meal->category_id = $request->name;
        // dd($meal);
        // dd($meal->category_id);

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $meal->save();

            // 画像アップロード
            if (!Storage::putFileAs('images/meals', $file, $meal->image)) {
                // if (!Storage::putFileAs('images/meals', $file, $meal->image)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('meals.show', $meal)
            ->with('notice', '記事を投稿しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $meal = Meal::find($id);
        if (Auth::user()) {
            $like = Like::where('meal_id', $meal->id)->where('user_id', auth()->user()->id)->first();
            // dd($like);
            return view('meals.show', compact('meal', 'like'));
        } else {
            return view('meals.show', compact('meal'));



            //      $like = Like::with('like')

            //             // $like = Like::where('meal', $meal->id)->where('user', auth()->user()->id)->first();
            //             return view('meals.show', compact('meal', 'like'));
            //         } else {
            //             return view('meals.show', compact('meal'));
            //         }
            //         return view('meals.show', compact('meal'));


            //         if (Auth::user()) {
            //             $like = Like::where('meal_id', $meal->id)->where('user_id', auth()->user()->id)->first();
            //             return view('meals.show', compact('meal', 'like'));
            //         } else {
            //             return view('meals.show', compact('meal'));
            //         }
            //     }
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::all();
        $meal = Meal::find($id);
        // dd($categories);
        return view('meals.edit', compact('categories', 'meal'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MealRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MealRequest $request, Meal $meal)
    {
        // $meal = new Meal($request->all());
        // $meal->category_id = $request->name;
        // $meal = Meal::find($id);

        // $meal->fill($request->all());
        $meal->category_id = $request->category_id;
        // dd($meal);
        // $meal->user_id = $request->user()->id;
        if ($request->user()->cannot('update', $meal)) {
            return redirect()->route('meals.show', $meal)
                // return redirect()->route('meals.show', $meal)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');
        
        if ($file) {
            $delete_file_path = $meal->image_path;
            $meal->image = self::createFileName($file);
        }
        $meal->fill($request->all());
        $meal->save();
        // トランザクション開始
        DB::beginTransaction();
        
        try {
        
            $meal->save();
            
            if ($file) {
            
                if (!Storage::putFileAs('images/meals', $file, $meal->image)) {
                    throw new \Exception('画像ファイルの削除に失敗しました。');
                }
                if (!Storage::delete($delete_file_path)) {
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }
                
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }
        
        return redirect()->route('meals.show', $meal)
            ->with('notice', '記事を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $meal = Meal::find($id);
        DB::beginTransaction();
        try {
            $meal->delete();

            if (!Storage::delete($meal->image_path)) {
                throw new \Exception('画像ファイルの削除に失敗しました。');
            }
            DB::commit();
        } catch (\Throwable $th) {
            return back()->withInput()->withErrors($th->getMessage());
        }

        return redirect()->route('meals.index')
            ->with('notice', '記事を削除しました');
    }

    private static function createFileName($file)
    {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
