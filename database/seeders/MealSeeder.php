<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $rg01Datas = [
            "op1" => "甘党",
            "op2" => "辛党",
            "op3" => "どちらでもない"
        ];
        $rg01Checked = "op2";

        $rg02Datas = [
            "opt1" => "あんこがギッシリ詰まった熱々のたい焼き",
            "opt2" => "クリームとかがはいった冷たい白たい焼き",
            "opt3" => "どちらも好きなので決められない"
        ];
        $rg02Checked = "opt2";

        $chkDatas = [
            "chk01" => "明るくノリノリのロック調の曲が好き",
            "chk02" => "渋いブルース風の曲が好き",
            "chk03" => "しみじみと心にしみるバラード曲がいいな"
        ];
        $chk01b = true;
        $chk02b = false;
        $chk03b = true;
        if ($chk01b) {
            $chkChecked["chk01"] = "checked";
        } else {
            $chkChecked["chk01"] = "";
        }

        if ($chk02b) {
            $chkChecked["chk02"] = "checked";
        } else {
            $chkChecked["chk02"] = "";
        }

        if ($chk03b) {
            $chkChecked["chk03"] = "checked";
        } else {
            $chkChecked["chk03"] = "";
        }

        return view('dummy', compact(
            'rg01Datas',
            'rg01Checked',
            'rg02Datas',
            'rg02Checked',
            'chkDatas',
            'chkChecked'
        ));
    }
}
