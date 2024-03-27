<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /*
    * create a method to list the Announcements the exist.
    */
    public function index()
    {
        $announcements = Announcement::all();
        return response()->json($announcements);
    }

    public function store(AnnouncementRequest $request)
    {
        $announcementData = $request->validated();

        $announcementData['organizer_id'] = auth()->id();

        $announcement = Announcement::create($announcementData);

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement created successfully',
            'announcement' => $announcement,
        ], 201);
    }

}
