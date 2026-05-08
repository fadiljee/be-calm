<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Traits\ApiResponse;

class QuestionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $questions = Question::where('is_active', true)->get();
        return $this->successResponse($questions, 'Active questions retrieved');
    }
}
