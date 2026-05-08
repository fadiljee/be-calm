<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    use ApiResponse;

    public function children(Request $request)
    {
        $children = User::where('parent_id', $request->user()->id)->get();
        return $this->successResponse($children, 'Children list retrieved');
    }

    public function childScores(Request $request, $student_id)
    {
        $child = User::where('id', $student_id)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();
            
        $scores = ScreeningHistory::where('student_id', $student_id)->latest()->get();
        
        return $this->successResponse([
            'child' => $child,
            'scores' => $scores
        ], 'Child scores retrieved');
    }
}
