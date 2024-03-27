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

        $requiredSkillsJson = json_encode($announcementData['required_skills']);

        $announcementData['organizer_id'] = auth()->id();

        $announcement = Announcement::create([
            'title' => $announcementData['title'],
            'type' => $announcementData['type'],
            'date' => $announcementData['date'],
            'description' => $announcementData['description'],
            'location' => $announcementData['location'],
            'required_skills' => $requiredSkillsJson,
            'organizer_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement created successfully',
            'announcement' => $announcement,
        ], 201);
    }

}
