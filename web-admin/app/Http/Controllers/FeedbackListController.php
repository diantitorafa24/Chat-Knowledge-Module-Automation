<?php

namespace App\Http\Controllers;

use App\Models\Feedback;

class FeedbackListController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('chatLog.module')->latest()->paginate(20);

        $total = Feedback::count();
        $positif = Feedback::where('rating', 1)->count();
        $rate = $total > 0 ? round($positif / $total * 100, 1) : 0;

        return view('feedback.index', compact('feedbacks', 'total', 'positif', 'rate'));
    }
}
