<?php

namespace App\Http\Controllers;

use App\Exports\QuestionExport;
use App\Http\Resources\QuestionResource;
use App\Models\Answer;
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
                'answer_id' => 'required|string',
                'kol_session_id' => 'required|exists:kol_sessions,id',
                'replay' => 'string|in:true,false'
            ]);

            $response = null;
            $request->answer_id = explode(",", $request->answer_id);

            if($validator->fails()) return sendFailResponse(Arr::flatten($validator->errors()->messages()));
            
            $response = Response::where([
                'attendee_id' => $request->attendee_id,
                'question_id' => $request->question_id,
                'kol_session_id' => $request->kol_session_id
            ])->pluck('answer_id')->toArray();

            if(!empty($response)) {                
                $remove = array_diff($response, $request->answer_id);
                $add = array_diff($request->answer_id, $response);
                // dd($remove, $add, $response);
                
                if(!empty($remove))
                    foreach($remove as $data) {
                        Response::where([
                            'attendee_id' => $request->attendee_id,
                            'question_id' => $request->question_id,
                            'kol_session_id' => $request->kol_session_id,
                            'answer_id' => $data
                        ])->delete();
                        DB::table('answer_sessions')->where('answer_id', $data)->where('kol_session_id', $request->kol_session_id)->update(['count' => DB::raw('count - '. 1)]);
                    }
                
                
                if(!empty($add))
                    foreach($add as $data) {
                        Response::create([
                            'attendee_id' => $request->attendee_id,
                            'question_id' => $request->question_id,
                            'kol_session_id' => $request->kol_session_id,
                            'answer_id' => $data
                        ]);
                        $query = DB::table('answer_sessions')->where('answer_id', $data)->where('kol_session_id', $request->kol_session_id);
                        if(empty($query->first()))
                            DB::table('answer_sessions')->insert(['answer_id' => $data, 'kol_session_id' => $request->kol_session_id, 'count' => 1]);
                        else
                            $query->update(['count' => DB::raw('count + '. 1)]);      
                    }
            } else {
               foreach($request->answer_id as $data) {
                    $response = Response::create([
                        'attendee_id' => $request->attendee_id,
                        'question_id' => $request->question_id,
                        'kol_session_id' => $request->kol_session_id,
                        'answer_id' => $data 
                    ]);   
               }

                DB::table('question_sessions')->updateOrInsert(
                    ['question_id' => $request->question_id, 'kol_session_id' => $request->kol_session_id],
                    ['count' => DB::raw('count + '. 1)]
                );

                foreach($request->answer_id as $data) {
                    DB::table('answer_sessions')->where('answer_id', $data)->where('kol_session_id', $request->kol_session_id)->update(['count' => DB::raw('count + '. 1)]);      
                };
            }

            if(!empty($response)) return sendSuccessResponse('Feedback Created');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return sendFailResponse($e->getMessage());
        }
    }

    public function getGraphData(Question $question, KolSession $kolSession)
    {
        try {
            $question_session = DB::table('question_sessions')->where(
                ['question_id' => $question->id, 'kol_session_id' => $kolSession->id],
            )->first();

            if(empty($question_session)) {
                $question->total_answered = 0;
            } else {
                $question->total_answered = $question_session->count;
            }

            $data['question'] = $question;
            $data['answers'] = DB::table('answers')
            ->where('answers.question_id', $question->id)
            ->leftJoin('answer_sessions', function($join) use ($kolSession) {
                $join->on('answers.id', 'answer_sessions.answer_id')
                ->where('answer_sessions.kol_session_id', $kolSession->id);
            })
            ->select('answers.id', 'answers.answer', DB::raw('IFNULL(`answer_sessions`.`count`, 0) AS count'))
            ->get();

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
