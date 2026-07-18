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
        // Tambahkan kategori baru kamu di dalam fungsi ini
        'category' => 'nullable|in:broken_home,kurang_peran_orang_tua,kecemasan_berlebih,lingkungan,stres_akademik,ekonomi',
        'is_active' => 'boolean',
    ]);

    $question = Question::create($request->all());
    return $this->successResponse($question, 'Question created successfully', 201);
}

public function update(Request $request, Question $question)
{
    $request->validate([
        'question_text' => 'sometimes|required',
        // Samakan juga di fungsi update agar saat edit tidak error
        'category' => 'nullable|in:broken_home,kurang_peran_orang_tua,kecemasan_berlebih,lingkungan,stres_akademik,ekonomi',
        'is_active' => 'boolean',
    ]);

    $question->update($request->all());
    return $this->successResponse($question, 'Question updated successfully');
}

    public function show(Question $question)
    {
        return $this->successResponse($question, 'Question detail retrieved');
    }


    public function destroy(Question $question)
    {
        $question->delete();
        return $this->successResponse(null, 'Question deleted successfully');
    }
}
