<?php

namespace App\Http\Controllers;

use App\Bids;
use Illuminate\Http\Request;
use DB;

class BidsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = date("Y-m-d"); //取得現在"年-月-日"
        DB::table('auctions')->where('end_date', $today)->update(['bid_function' => "關閉"]);//關閉'end_date'與現在"年-月-日"相同的拍賣品投注功能
        //取得今天到期和投注功能關閉的拍賣品資料
        $post = DB::table("auctions")
                    ->where('end_date', $today)
                    ->orWhere('bid_function', "關閉")
                    ->get();
        //更新拍賣品狀態
        foreach ($post as $variable) {
            //廢標條件
            if ($variable->quantity < $variable->minimum){
                DB::table('auctions')->where('id', $variable->id)->update(['situation' => "廢標"]);
                //取得投注該拍賣品之會員投注之數量
                $post2 = DB::table("userbidauctions")->where('auction_id', $variable->id)->get();
                //將投注的數量退回給對應之會員
                foreach ($post2 as $variable2) {
                    if ($variable->situation != "廢標" && $variable2->refund_status != "已退費"){
                        $current_data = DB::table("users")->where('id',$variable2->user_id)->value('tokens_quantity');//會員目前代幣數量
                        $bid_quantity_data = DB::table("userbidauctions")->where('auction_id', $variable->id)->where('user_id', $variable2->user_id)->value('bid_quantity');//會員投注退回該拍賣的數量
                        $final_data = $current_data + $bid_quantity_data;//退回該拍賣品的投注數量之後的代幣數量
                        DB::table('users')->where('id', $variable2->user_id)->update(['tokens_quantity' => "$final_data"]);

                        //變更該筆投注資料的”退費狀態”為”已退費”
                        DB::table('userbidauctions')->where('auction_id', $variable2->auction_id)->update(['refund_status' => "已退費"]);
                    }
                }
                //變更該拍賣品的”中獎號碼”為”9487”
                DB::table('auctions')->where('id', $variable->id)->update(['winning_numbers' => '9487']);
            }
            //開標
            if ($variable->quantity >= $variable->minimum){
                DB::table('auctions')->where('id', $variable->id)->update(['situation' => "截標"]);

                //產生中獎號碼
                $winning_numbers = '1';
                //取得該拍賣品全部流水號碼
                $post3 = DB::table("userbidserial_number")->where('auction_id', $variable->id)->get();   
                //檢查該拍賣品的每筆流水號
                foreach ($post3 as $variable3) {
                    //取得該拍賣品目前會員的流水號
                    $user_serial_number = DB::table("userbidserial_number")->where('user_id', $variable3->user_id)->value('serial_number');
                    //找出對應中獎號碼的流水號碼之會員
                    if ($winning_numbers == $user_serial_number) {
                        //填入中獎號碼至資料表"auctions"的欄位"winning_numbers"
                        DB::table('auctions')->where('id', $variable3->auction_id)->update(['winning_numbers' => $user_serial_number]);
                        DB::table('userbidserial_number')->where('user_id', $variable3->user_id)->where('auction_id', $variable->id)->update(['YorN' => "中獎"]);
                        //新增中獎資料至資料表”winners”
                        $user_id_data = DB::table("userbidserial_number")->where('auction_id', $variable->id)->where('serial_number', $winning_numbers)->value('user_id');
                        $auction_id_data = DB::table("userbidserial_number")->where('auction_id', $variable->id)->where('serial_number', $winning_numbers)->value('auction_id');
                        $now = date("Y-m-d H:i:s");
                        DB::table('winners')->insert(array('user_id' => $user_id_data, 'auction_id' => $auction_id_data, 'date' => $now, 'winning_number' =>$user_serial_number));
                    }
                    else{
                        DB::table('userbidserial_number')->where('user_id', $variable3->user_id)->where('auction_id', $variable->id)->update(['YorN' => "未中獎"]);
                    }
                }
            }
        }

        $data=['auctions'=>$post];
        return view('bids.dashboard.index', $data);
    }
}