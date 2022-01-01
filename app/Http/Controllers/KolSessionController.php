<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\KolSession;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KolSessionController extends Controller
{
    public function validateSession(KolSession $kolSession)
    {
        try {
            // dd(date("Y-m-d H:i:s", strtotime('+10 minutes')));
            $sessionStartDateTime = Carbon::createFromDate($kolSession->start_date_time);
            $currentDateTime = Carbon::createFromDate(now());
            $diffInMinutes = $sessionStartDateTime->diffInMinutes($currentDateTime);
            if (($sessionStartDateTime->isPast() && $diffInMinutes > 5) || $diffInMinutes > 15) return response('Session is not valid for login', 400);

            return response('Session is valid', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response($e->getMessage(), 500);
        }
    }

    public function loginAttendee(Request $request, KolSession $kolSession)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|string|min:10|max:10'
            ]);

            if ($validator->fails()) return response(Arr::flatten($validator->errors()->messages()), 400);

            $attendee = Attendee::where('phone', $request->phone)->first();

            if (!$attendee)
                $attendee = Attendee::create([
                    'name' => $request->name,
                    'phone' => $request->phone
                ]);

            DB::table('attendee_session')->updateOrInsert(
                [
                    'attendee_id' => $attendee->id,
                    'kol_session_id' => $kolSession->id
                ],
                [
                    'created_at' => new DateTime(),
                    'updated_at' => new DateTime()
                ]
            );

            if ($attendee) return response($attendee, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response($e->getMessage(), 500);
        }
    }
}
