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

    // app/Http/Controllers/Api/Parent/ChildController.php

public function childScores(Request $request, $student_id)
{
    // Validasi apakah benar ini anak dari orang tua yang login
    User::where('id', $student_id)
        ->where('parent_id', $request->user()->id)
        ->firstOrFail();
        
    // Ambil history-nya saja secara langsung
    $scores = ScreeningHistory::where('student_id', $student_id)->latest()->get();
    
    // Kirim $scores langsung sebagai data utama
    return $this->successResponse($scores, 'Child scores retrieved');
}
}
