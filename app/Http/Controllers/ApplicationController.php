<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Models\Announcement;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /*
     * method for volunteer to apply to a volunteer initiative
     */
    public function store(ApplicationRequest $request)
    {
        $validatedData = $request->validated();

        $application = new Application();
        $application->announcement_id = $validatedData['announcement_id'];
        $application->volunteer_id = $validatedData['volunteer_id'];
        $application->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ]);
    }

    /*
     * method for organizer to see the applications of volunteers
     */
    public function index()
    {
        $organizerId = auth()->id();

        $pendingApplications = Application::whereHas('announcement', function ($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })->where('status', 'pending')->get();

        if ($pendingApplications->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No pending applications found for the organizer.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'pending_applications' => $pendingApplications,
        ]);
    }

    /*
     * method for organizer to approve or refuse the applications of volunteers
     */
    public function manageApplications(Application $application, Request $request)
    {
        if ($application->announcement->organizer_id !== auth()->id())
        {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:approved,refused',
        ]);

        $application->status = $validatedData['status'];
        $application->save();

        $message = ($application->status === 'approved') ? 'Application approved successfully.' : 'Application rejected successfully.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'application' => $application,
        ]);
    }

}
