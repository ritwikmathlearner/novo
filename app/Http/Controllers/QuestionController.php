<?php

namespace App\Http\Controllers;

use App\Exports\QuestionExport;
use App\Http\Resources\QuestionResource;
use App\Models\Attendee;
use App\Models\KolSession;
use App\Models\Question;
use App\Models\Response;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function index($case)
    {
        try {
            $questions = QuestionResource::collection(Question::where('patient_case', $case)->get());
            return sendSuccessResponse(null, $questions);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }

    public function response(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attendee_id' => 'required|exists:attendees,id',
                'question_id' => 'required|exists:questions,id',
                'answer_id' => 'required|exists:answers,id',
                'kol_session_id' => 'required|exists:kol_sessions,id'
            ]);

            if($validator->fails()) return sendFailResponse(Arr::flatten($validator->errors()->messages()));

            DB::table('questions')->where('id', $request->question_id)
            ->update(['total_answered' => DB::raw('total_answered + '. 1)]);

            DB::table('answers')->where('id', $request->answer_id)
            ->update(['count' => DB::raw('count + '. 1)]);

            $feedback = Response::create([
                'attendee_id' => $request->attendee_id,
                'question_id' => $request->question_id,
                'answer_id' => $request->answer_id,
                'kol_session_id' => $request->kol_session_id
            ]);

            if(!empty($feedback)) return sendSuccessResponse('Feedback Created');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }

    public function getGraphData(Question $question)
    {
        try {
            $data['question'] = $question;
            $data['answers'] = $question->answers()->get();

            return sendSuccessResponse(null, $data);            
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }

    public function export(Attendee $attendee, KolSession $kolSession)
    {
        $fileName = $attendee->name. '-' . $kolSession->session_name . '-' . Carbon::parse($kolSession->start_date_time)->format('d-m-Y') .'-Question.xlsx';

        return Excel::download(new QuestionExport($attendee->id, $kolSession->id), $fileName);
    }
}
