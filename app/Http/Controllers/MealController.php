<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealRequest;
use App\Models\Meal;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        // $meals = Meal::latest()->get();
        // 現在の日時
        // $timestamp = 1599613200;

        // return view('index', [
        //     'timestamp' => $timestamp,
        // ]);

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

        $meal->category_id = $request->name;
        // dd($meal);
        // dd($meal->category_id);

        $file = $request->file('image');
        $meal->image = self::createFileName($file);
        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $meal->save();

            // 画像アップロード
            if (!Storage::putFileAs('images/meals', $file, $meal->image)) {
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

        return view('meals.show', compact('meal'));
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
    public function update(MealRequest $request, $id)
    {
        $meal = Meal::find($id);
        $meal->category_id = $request->name;

        if ($request->user()->cannot('update', $meal)) {
            return redirect()->route('meals.show', $meal)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');
        if ($file) {
            $delete_file_path = $meal->image_path;
            $meal->image = self::createFileName($file);
        }
        $meal->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 更新
            $meal->save();

            if ($file) {
                // 画像アップロード
                if (!Storage::putFileAs('images/meals', $file, $meal->image)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }

                // 画像削除
                if (!Storage::delete($delete_file_path)) {
                    //アップロードした画像を削除する
                    Storage::delete($meal->image_path);
                    //例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの削除に失敗しました。');
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
    public function destroy(Meal $meal)
    {

        try {
            $meal->delete();

            if (!Storage::delete($meal->image_path)) {
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }
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

