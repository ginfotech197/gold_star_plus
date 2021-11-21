<?php

namespace App\Http\Controllers;

use App\Http\Resources\ManualResultResource;
use App\Models\GameType;
use App\Models\ManualResult;
use App\Models\ResultMaster;
use App\Models\TwoDigitNumberCombinations;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManualResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function save_manual_result(Request $request)
    {
//        $rules = array(
//            'drawMasterId'=>['required','exists:draw_masters,id',
//                    function($attribute, $value, $fail){
//                        $existingManual=ManualResult::where('draw_master_id', $value)->where('game_date','=',Carbon::today())->first();
//                        if($existingManual){
//                            $fail($value.' Duplicate entry');
//                        }
//                    }
//                ],
////            'numberCombinationId'=>'required|exists:number_combinations,id',
//        );
//        $validator = Validator::make($request->all(),$rules);
//
//        if($validator->fails()){
//            return response()->json(['success'=>0,'data'=>null,'error'=>$validator->messages()], 406,[],JSON_NUMERIC_CHECK);
//        }
        $requestedData = $request->json()->all();
//        return response()->json(['success'=>1,'data'=> $requestedData], 200,[],JSON_NUMERIC_CHECK);
//        DB::beginTransaction();
//        try{
//        $requestedData.foreach ()
        foreach ($requestedData as $items) {
            $twoNumberCombination = TwoDigitNumberCombinations::select()->where('visible_number',$items['twoDigitNumberCombinationId'] )->first();
            $manualResult = new ManualResult();
            $manualResult->draw_master_id = $items['drawMasterId'];
            $manualResult->two_digit_number_combination_id = $twoNumberCombination->id;
            $manualResult->game_type_id = $items['gameTypeId'];
            $manualResult->game_date = Carbon::today();
            $manualResult->save();
        }

//            DB::commit();
//        }catch (\Exception $e){
//            DB::rollBack();
//            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
//        }

//        return response()->json(['success'=>1,'data'=> new ManualResultResource($manualResult)], 200,[],JSON_NUMERIC_CHECK);
        return response()->json(['success'=>1,'data'=> $manualResult], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_load_details($id)
    {

//        $data = [
//            'id' => 1,
//            'game_id' => 2
//        ];
//
//        return response()->json(['success'=> 1, 'data' => $data], 200);

//        return response()->json(['success'=> 1, 'data' => $id], 200);

//        return response()->json(['success'=> 1, 'data' => $request], 200);
//        $data = DB::select("select game_types.id ,game_types.game_name, two_digit_number_sets.number_set, sum(play_details.quantity) as total from play_masters
//                inner join play_details on play_details.play_master_id = play_masters.id
//                inner join game_types on game_types.id = play_details.game_type_id
//                inner join two_digit_number_sets on two_digit_number_sets.id = play_details.two_digit_number_set_id
//                inner join draw_masters on draw_masters.id = play_masters.draw_master_id
//                group by  game_types.id ,game_types.game_name, two_digit_number_sets.number_set");

        $gameTypes = GameType::select('id')->get();
        $data1 = [];
        $tempDate = [];
        foreach ($gameTypes as $gameType){
            $data = DB::select("select sum(play_details.quantity) as total from play_masters
                inner join play_details on play_details.play_master_id = play_masters.id
                inner join game_types on game_types.id = play_details.game_type_id
                inner join two_digit_number_sets on two_digit_number_sets.id = play_details.two_digit_number_set_id
                inner join draw_masters on draw_masters.id = play_masters.draw_master_id
                where game_types.id = ".$gameType->id." and draw_masters.id = ".$id."
                group by  game_types.id ,game_types.game_name, two_digit_number_sets.number_set
                ");
            if(!$data){
                for($i = 0; $i <= 9; $i++){
                    $data = [
                        'total' => 0
                    ];
                    array_push($tempDate, $data);
                }
                $data = $tempDate;
            };
            array_push($data1, $data);
        }
        return response()->json(['success'=> 1, 'data' => $data1], 200);

    }

    public function show(ManualResult $manualResult)
    {
        //
    }

    public function edit(ManualResult $manualResult)
    {
        //
    }

    public function update(Request $request, ManualResult $manualResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ManualResult  $manualResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManualResult $manualResult)
    {
        //
    }
}
