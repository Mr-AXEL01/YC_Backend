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
    public function index(Request $request)
    {
        $eventType = $request->query('type');
        $location = $request->query('location');

        $query = Announcement::query();

        if ($eventType) {
            $query->where('type', $eventType);
        }
        if ($location) {
            $query->where('location', $location);
        }

        $announcements = $query->get();

        if ($announcements->isEmpty()) {
            $message = 'No announcements found';

            if ($eventType && $location) {
                $message .= ' for the specified type and location.';
            } elseif ($eventType) {
                $message .= ' for the specified type.';
            } elseif ($location) {
                $message .= ' for the specified location.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'announcements' => $announcements,
        ]);
    }

    /*
    * store new Announcement.
    */

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
