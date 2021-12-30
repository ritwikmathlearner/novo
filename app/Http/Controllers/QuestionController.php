<?php

namespace App\Http\Controllers;

use App\Exports\QuestionExport;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Response;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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
            return $questions;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function response(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'question_id' => 'required|exists:questions,id',
                'answer_id' => 'required|exists:answers,id',
            ]);

            if($validator->fails()) return false;

            DB::table('questions')->where('id', $request->question_id)
            ->update(['total_answered' => DB::raw('total_answered + '. 1)]);

            DB::table('answers')->where('id', $request->answer_id)
            ->update(['count' => DB::raw('count + '. 1)]);

            $feedback = Response::create([
                'user_id' => $request->user_id,
                'question_id' => $request->question_id,
                'answer_id' => $request->answer_id
            ]);

            if(!empty($feedback)) return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getGraphData(Question $question)
    {
        try {
            $data['question'] = $question;
            $data['answers'] = $question->answers()->get();

            return $data;            
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function export(User $user)
    {
        return Excel::download(new QuestionExport($user->id), 'Question.xlsx');
    }
}
