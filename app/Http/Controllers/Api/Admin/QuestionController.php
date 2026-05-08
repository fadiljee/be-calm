<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $questions = Question::all();
        return $this->successResponse($questions, 'Questions retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required',
            'category' => 'nullable|in:stress,anxiety,depression',
            'is_active' => 'boolean',
        ]);

        $question = Question::create($request->all());
        return $this->successResponse($question, 'Question created successfully', 201);
    }

    public function show(Question $question)
    {
        return $this->successResponse($question, 'Question detail retrieved');
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'sometimes|required',
            'category' => 'nullable|in:stress,anxiety,depression',
            'is_active' => 'boolean',
        ]);

        $question->update($request->all());
        return $this->successResponse($question, 'Question updated successfully');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return $this->successResponse(null, 'Question deleted successfully');
    }
}
