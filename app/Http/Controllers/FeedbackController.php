<?php

namespace App\Http\Controllers;

use App\Exports\FeedbackExport;
use App\Models\Feedback;
use App\Models\KolSession;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackController extends Controller
{
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kol_session_id' => 'required|exists:kol_sessions,id',
                'attendee_id' => 'required|exists:attendees,id',
                'feedback' => 'required|int|min:1|max:5'
            ]);

            if($validator->fails()) return sendFailResponse(Arr::flatten($validator->errors()->messages()));

            $feedback = Feedback::create([
                'kol_session_id' => $request->kol_session_id,
                'attendee_id' => $request->attendee_id,
                'feedback' => $request->feedback
            ]);

            if(!empty($feedback)) return sendSuccessResponse("Feedback Stored");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }

    public function export(KolSession $kolSession)
    {
        $fileName = $kolSession->session_name. '-' . Carbon::parse($kolSession->start_date_time)->format('d-m-Y') .'-Feedbacks.xlsx';
        return Excel::download(new FeedbackExport($kolSession->id), $fileName);
    }
}
