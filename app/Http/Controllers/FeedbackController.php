<?php

namespace App\Http\Controllers;

use App\Exports\FeedbackExport;
use App\Models\Feedback;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackController extends Controller
{
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'feedback' => 'required|string|min:3'
            ]);

            if($validator->fails()) return false;

            $feedback = Feedback::create([
                'user_id' => $request->user_id,
                'feedback' => $request->feedback
            ]);

            if(!empty($feedback)) return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function export()
    {
        return Excel::download(new FeedbackExport, 'Feedbacks.xlsx');
    }
}
