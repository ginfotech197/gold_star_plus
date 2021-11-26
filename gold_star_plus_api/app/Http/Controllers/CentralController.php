<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManualResult;
use App\Models\NumberCombination;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\ResultMaster;
use Illuminate\Http\Request;
use App\Models\NextGameDraw;
use App\Models\DrawMaster;
use App\Http\Controllers\PlayMasterController;
use App\Http\Controllers\NumberCombinationController;
use App\Models\GameType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CentralController extends Controller
{
    public function createResult(){

        $today= Carbon::today()->format('Y-m-d');
        $nextGameDrawObj = NextGameDraw::first();
        $nextDrawId = $nextGameDrawObj->next_draw_id;
        $lastDrawId = $nextGameDrawObj->last_draw_id;
        $playMasterControllerObj = new PlayMasterController();

        $playMasterObj = new TerminalReportController();
        $playMasterObj->updateCancellation();
        $totalGame = GameType::count();

        for($i=1;$i<=$totalGame;$i++) {
            $totalSale = $playMasterControllerObj->get_total_sale($today, $lastDrawId, $i);
            $gameType = GameType::find($i);
            $payout = ($totalSale * ($gameType->payout)) / 100;
            $targetValue = floor($payout / $gameType->winning_price);
            // result less than equal to target value
            $result = DB::select(DB::raw("select two_digit_number_sets.id as two_digit_number_set_id,two_digit_number_sets.number_set,
two_digit_number_combinations.id as two_digit_number_combination_id,
sum(play_details.quantity) as total_quantity
from play_details
inner join play_masters ON play_masters.id = play_details.play_master_id
inner join two_digit_number_sets ON two_digit_number_sets.id = play_details.two_digit_number_set_id
inner join two_digit_number_combinations on two_digit_number_combinations.two_digit_number_set_id = two_digit_number_sets.id
where play_masters.draw_master_id = $lastDrawId and play_details.game_type_id=$i and date(play_details.created_at)= " . "'" . $today . "'" . "
group by two_digit_number_sets.number_set,two_digit_number_sets.id,two_digit_number_combinations.id
having sum(play_details.quantity)<= $targetValue
order by rand() limit 1"));

            // select empty item for result
            if (empty($result)) {
                // empty value
                $result = DB::select(DB::raw("SELECT two_digit_number_sets.id as two_digit_number_set_id,two_digit_number_combinations.id as two_digit_number_combination_id
FROM two_digit_number_sets
inner join two_digit_number_combinations on two_digit_number_combinations.two_digit_number_set_id = two_digit_number_sets.id
WHERE two_digit_number_sets.id NOT IN(SELECT DISTINCT
play_details.two_digit_number_set_id FROM play_details
INNER JOIN play_masters on play_details.play_master_id= play_masters.id
WHERE  DATE(play_masters.created_at) = " . "'" . $today . "'" . " and play_masters.draw_master_id = $lastDrawId
and play_details.game_type_id=$i)
ORDER by rand() LIMIT 1"));
            }

            // result greater than equal to target value
            if (empty($result)) {
                $result = DB::select(DB::raw("select two_digit_number_sets.id as two_digit_number_set_id,two_digit_number_sets.number_set,
two_digit_number_combinations.id as two_digit_number_combination_id,
sum(play_details.quantity) as total_quantity
from play_details
inner join play_masters ON play_masters.id = play_details.play_master_id
inner join two_digit_number_sets ON two_digit_number_sets.id = play_details.two_digit_number_set_id
inner join two_digit_number_combinations on two_digit_number_combinations.two_digit_number_set_id = two_digit_number_sets.id
where play_masters.draw_master_id = $lastDrawId and play_details.game_type_id=$i and date(play_details.created_at)= " . "'" . $today . "'" . "
group by two_digit_number_sets.number_set,two_digit_number_sets.id,two_digit_number_combinations.id
having sum(play_details.quantity)>= $targetValue
order by rand() limit 1"));
            }


            $two_digit_result_id = $result[0]->two_digit_number_combination_id;

            DrawMaster::query()->update(['active' => 0]);
            if (!empty($nextGameDrawObj)) {
                DrawMaster::findOrFail($nextDrawId)->update(['active' => 1]);
            }


            $resultMasterController = new ResultMasterController();
            $jsonData = $resultMasterController->save_auto_result($lastDrawId, $two_digit_result_id, $gameType->id);
        }

        $resultCreatedObj = json_decode($jsonData->content(),true);

//        $actionId = 'score_update';
//        $actionData = array('team1_score' => 46);
//        event(new ActionEvent($actionId, $actionData));

        if( !empty($resultCreatedObj) && $resultCreatedObj['success']==1){

            $totalDraw = DrawMaster::count();
            if($nextDrawId==$totalDraw){
                $nextDrawId = 1;
            }
            else {
                $nextDrawId = $nextDrawId + 1;
            }

            if($lastDrawId==$totalDraw){
                $lastDrawId = 1;
            }
            else{
                $lastDrawId = $lastDrawId + 1;
            }

            $nextGameDrawObj->next_draw_id = $nextDrawId;
            $nextGameDrawObj->last_draw_id = $lastDrawId;
            $nextGameDrawObj->save();

            return response()->json(['success'=>1, 'message' => 'Result added'], 200);
        }else{
            return response()->json(['success'=>0, 'message' => 'Result not added'], 401);
        }

    }



    public function createResultByDate(){

        $today= '2021-09-02';
        $nextGameDrawObj = NextGameDraw::first();
        $nextDrawId = 7;
        $lastDrawId = 6;
        $playMasterControllerObj = new PlayMasterController();

        $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId);
        $single = GameType::find(1);

        $payout = ($totalSale*($single->payout))/100;
        $targetValue = floor($payout/$single->winning_price);
        echo $targetValue;

        // result less than equal to target value
        $result = DB::select(DB::raw("select single_numbers.id as single_number_id,single_numbers.single_number,sum(play_details.quantity) as total_quantity  from play_details
        inner join play_masters ON play_masters.id = play_details.play_master_id
        inner join single_numbers ON single_numbers.id = play_details.single_number_id
        where play_masters.draw_master_id = $lastDrawId  and date(play_details.created_at)= "."'".$today."'"."
        group by single_numbers.single_number,single_numbers.id
        having sum(play_details.quantity)<= $targetValue
        order by rand() limit 1"));

        echo 'Check1';
        print_r($result);
        if(empty($result)){
            // empty value
            $result = DB::select(DB::raw("SELECT single_numbers.id as single_number_id FROM single_numbers WHERE id NOT IN(SELECT DISTINCT
        play_details.single_number_id FROM play_details
        INNER JOIN play_masters on play_details.play_master_id= play_masters.id
        WHERE  DATE(play_masters.created_at) = "."'".$today."'"." and play_masters.draw_master_id = $lastDrawId) ORDER by rand() LIMIT 1"));
        }
        echo 'Check2';
        print_r($result);

        if(empty($result)){
            $result = DB::select(DB::raw("select single_numbers.id as single_number_id,single_numbers.single_number,sum(play_details.quantity) as total_quantity  from play_details
            inner join play_masters ON play_masters.id = play_details.play_master_id
            inner join single_numbers ON single_numbers.id = play_details.single_number_id
            where play_masters.draw_master_id= $lastDrawId  and date(play_details.created_at)= "."'".$today."'"."
            group by single_numbers.single_number,single_numbers.id
            having sum(play_details.quantity)>= $targetValue
            order by rand() limit 1"));
        }

        echo 'Check3';
        print_r($result);

        $single_number_result_id = $result[0]->single_number_id;


        $resultMasterController = new ResultMasterController();
        $jsonData = $resultMasterController->save_auto_result($lastDrawId,$single_number_result_id);

        $resultCreatedObj = json_decode($jsonData->content(),true);

//        $actionId = 'score_update';
//        $actionData = array('team1_score' => 46);
//        event(new ActionEvent($actionId, $actionData));

        if( !empty($resultCreatedObj) && $resultCreatedObj['success']==1){

            return response()->json(['success'=>1, 'message' => 'Result added'], 200);
        }else{
            return response()->json(['success'=>0, 'message' => 'Result not added'], 401);
        }

    }

    public function drawWiseReport(Request $request){

        $requestedData = (object)$request->json()->all();
        $gameTypeId = $requestedData->gameType;
        $today = $requestedData->date;
//        return response()->json(['success' => 1,'data' => $requestedData->drawTime], 200);

        $drawMasters = DrawMaster::select()->get();
        $gameTypes = GameType::select()->where('id',$gameTypeId)->first();

        //date declaration
//        $today = Carbon::today()->format('Y-m-d');
//        $today = "2021-11-25";

        //array declaration for final report
        $dataReport = [];

        //variable declaration
        $total_prize = 0;
        $total_quantity = 0;
        $payout = 0;

//        $playMaster = PlayMaster::select()->where('draw_master_id',1)->whereRaw('date(play_masters.created_at) >= ?', $today)->get();
//        $payout = PlayDetails::select('payout')->where('play_master_id',$playMaster[0]->id)->first();
//
//        return response()->json(['$payout'=>$playMaster[0]->id, '$drawMasters' => $payout, '$dataReport' => $dataReport], 200);

        //object created
        $cPanelReportController = new CPanelReportController();

        //array declaration
        $playMaster = [];

        foreach ($drawMasters as $drawMaster){
            $total_prize = 0;
            $total_quantity = 0;
//            $payout = 0;

            $playMaster = PlayMaster::select()->where('draw_master_id',$drawMaster->id)->whereRaw('date(play_masters.created_at) >= ?', $today)->get();

            foreach ($playMaster as $newPlayMaster){
                $total_prize = $total_prize + ($cPanelReportController->get_prize_value_by_barcode($newPlayMaster->id));
                $total_quantity = $cPanelReportController->get_total_quantity_by_barcode($newPlayMaster->id);
                $payout = PlayDetails::select('payout')->where('play_master_id',$newPlayMaster->id)->first();
            }

            $result_details = ResultMaster::select('two_digit_number_combinations.visible_number')
                ->join('result_details','result_details.result_masters_id','result_masters.id')
                ->join('two_digit_number_combinations','result_details.two_digit_number_combination_id','two_digit_number_combinations.id')
                ->where('result_masters.draw_master_id', $drawMaster->id)
                ->where('result_details.game_type_id', $gameTypes->id)
                ->whereRaw('date(result_masters.created_at) >= ?', $today)
                ->first();

            $manual_result = ManualResult::select()
                ->join('two_digit_number_combinations','manual_results.two_digit_number_combination_id','two_digit_number_combinations.id')
                ->where('manual_results.draw_master_id', $drawMaster->id)
                ->where('manual_results.game_type_id', $gameTypes->id)
                ->whereRaw('date(manual_results.created_at) >= ?', $today)
                ->first();


            $tempData = [
                'game_name' => $gameTypes->game_name,
                'draw_time' => $drawMaster->visible_time,
                'mrp' => $gameTypes->mrp,
                'prize_value' => $total_prize,
                'total_sale' => ($total_quantity * $gameTypes->mrp),
                'result' => $result_details?$result_details->visible_number : 0,
                'payout_on_sales' => ($total_quantity * $gameTypes->mrp)?(($total_prize / ($total_quantity * $gameTypes->mrp))*100) : 0,
                'manual_result' => $manual_result?$manual_result->visible_number : null,
                'payout' => $payout->payout
            ];
            array_push($dataReport, $tempData);
        }


//        return response()->json(['$payout'=>$payout->payout, '$drawMasters' => $playMaster, '$dataReport' => $dataReport], 200);
        return response()->json(['success' => 1,'data' => $dataReport], 200);
    }

}
