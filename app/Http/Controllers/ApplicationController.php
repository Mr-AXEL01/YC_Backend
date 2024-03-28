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
     * method for organizer to approve or refuse the applications of volunteers
     */
    public function manageApplications(Announcement $announcement, Application $application, Request $request)
    {
        if ($announcement->organizer_id !== auth()->id()) {
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
