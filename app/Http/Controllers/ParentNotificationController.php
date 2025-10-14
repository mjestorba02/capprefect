<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Sanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ParentNotificationController extends Controller
{
    public function index()
    {
        // Fetch all students who have at least one sanction
        $studentsWithCases = Student::with(['sanctions'])
            ->whereHas('sanctions')
            ->get();

        return view('notifications.parents', compact('studentsWithCases'));
    }

    public function notify(Request $request)
    {
        $studentName = $request->student_name;
        $parentEmail = $request->parent_email;
        $violation = $request->violation;
        $messageBody = $request->message;

        // Send email with HTML formatting
        Mail::html("
            <p>Dear Parent/Guardian,</p>
            <p>This is to inform you that your child, <strong>$studentName</strong>, has a disciplinary case.</p>
            <p><strong>Violation:</strong> $violation</p>
            <p><strong>Message from the Office:</strong><br>$messageBody</p>
            <p>Sincerely,<br><strong>Discipline Office</strong></p>
        ", function ($message) use ($parentEmail, $studentName) {
            $message->to($parentEmail)
                    ->subject("Notification Regarding $studentName");
        });

        return redirect()->back()->with('success', "Parent of $studentName has been notified successfully!");
    }
}