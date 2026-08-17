<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminInquiryModel;
use Inertia\Inertia;
use Inertia\Response;

class AdminInquiryController extends Controller
{
    public function index(): Response
    {
        $inquiries = AdminInquiryModel::latest()
            ->paginate(10)
            ->through(fn($inquiry) => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'subject' => $inquiry->subject,
                'message' => $inquiry->message,
                'created_at' => $inquiry->created_at->format('d M Y, h:i A'),
            ]);

        return Inertia::render('Admin/Inquiry', [
            'inquiries' => $inquiries,
        ]);
    }
}
