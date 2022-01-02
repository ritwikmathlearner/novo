<?php

namespace App\Http\Controllers;

use App\Models\KolSession;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class KoluserController extends Controller
{
    //
    
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'unique_code' => 'required|exists:kol_sessions,unique_code'
            ]);
            
            if($validator->fails()) return response(Arr::flatten($validator->errors()->messages()), 400);
            
            /* 
             * 1    Success  
             * 10   Wrong code
             * 11   Yet to start
             * 12   Session closed
            */
            $ResposeData = [
                    'status' => 0,
                    'message' => '',
                    'data' => []
                ]; 
                    
            $kol_sessions_data = KolSession
                    ::select('kol_sessions.id as session_id ',
                            'kol_sessions.session_name',
                            'kol_sessions.start_date_time',
                            'kol_sessions.end_date_time',
                            'kol_sessions.session_ended_by',
                            'users.name',
                            'users.email'
                            )
                    ->join('users', 'users.id', '=', 'kol_sessions.user_id')
                    ->where('unique_code', '123456')    //$request->unique_code
                    ->first();
            
            if(empty($kol_sessions_data)){
                $ResposeData = [
                    'status' => 10,
                    'message' => 'Wrong code.',
                    'data' => []
                ]; 
            }
            else{
                unset($kol_sessions_data->session_ended_by);
                $ResposeData['data'] = json_decode(json_encode($kol_sessions_data), true);

                if(!empty($kol_sessions_data->end_date_time)){
                    $ResposeData['status'] = 12;
                    $ResposeData['message'] = 'Session closed.';
                }
                else{
                    $start_date_time = strtotime($kol_sessions_data->start_date_time);
                    $currentTime = time();
//                    $ResposeData['start_date_time'] = $start_date_time;
//                    $ResposeData['currentTime'] = date('Y-m-d H:i:s');
//                    $ResposeData['timediff'] = $start_date_time - $currentTime;
                    if($start_date_time > $currentTime - 600 && $start_date_time < $currentTime + 300){
                        $ResposeData['status'] = 1;
                        $ResposeData['message'] = 'Login Success!';
                    }
                    else{
                        $ResposeData['status'] = 11;
                        $ResposeData['message'] = 'Session is yet to start.';
                    }
                }
                
            }
            
            if(!empty($ResposeData)) return response($ResposeData, 200);
            

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response($e->getMessage(), 500);
        }
    }
}
