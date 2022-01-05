<?php

namespace App\Http\Controllers;

use App\Models\KolSession;
use App\Models\User;

use Carbon\Carbon;
use DateTime;

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
                'unique_code' => 'required|exists:kol_sessions,unique_code',
                'login_for' => 'required'
            ]);
            
            if($validator->fails()) return sendFailResponse(Arr::flatten($validator->errors()->messages()));
            
            
            if('KOL' != $request->login_for){
                return sendFailResponse('Wrong user!');
            }
            
            /* 
             * 1    Success  
             * 10   Wrong code
             * 11   Session closed
             * 12   Yet to start
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
                    ->where('unique_code', $request->unique_code)    
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
                $ResposeData['data']['currentTime'] = date('Y-m-d H:i:s');
                
                if(!empty($kol_sessions_data->end_date_time)){
                    $ResposeData['status'] = 11;
                    $ResposeData['message'] = 'Session is closed.';
                }
                else{
//                    $start_date_time = strtotime($kol_sessions_data->start_date_time);
                    $sessionStartDateTime = Carbon::createFromDate($kol_sessions_data->start_date_time);
                    $currentDateTime = Carbon::createFromDate(now());
                    $diffInMinutes = $sessionStartDateTime->diffInMinutes($currentDateTime);
                    $ResposeData['data']['diffInMinutes'] = $diffInMinutes;
                    
//                    if (($sessionStartDateTime->isPast() && $diffInMinutes > 5) || $diffInMinutes > 15) return response('Session is not valid for login', 400);

                    if (($sessionStartDateTime->isPast() && $diffInMinutes > 5) || $diffInMinutes > 15){
                        $ResposeData['status'] = 12;
                        $ResposeData['message'] = 'Session is invalid.';
                        
                    }
                    else{
                        $ResposeData['status'] = 1;
                        $ResposeData['message'] = 'Login Success!';
                    }
                }
                
            }
            
//            if(!empty($ResposeData)) return response($ResposeData, (1 == $ResposeData['status'] ? 200 : 400));
            if(1 == $ResposeData['status']){
                return sendSuccessResponse(null, $ResposeData);
            }
            else{
                return sendFailResponse( $ResposeData);
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }
}
