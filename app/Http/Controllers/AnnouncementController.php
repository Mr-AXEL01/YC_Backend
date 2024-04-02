<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;


/**
 * @OA\Schema(
 *     schema="Announcement",
 *     title="Announcement",
 *     description="Announcement schema",
 *     @OA\Property(property="id", type="integer", description="ID of the announcement"),
 *     @OA\Property(property="title", type="string", description="Title of the announcement"),
 *     @OA\Property(property="type", type="string", description="Type of the announcement"),
 * )
 */
class AnnouncementController extends Controller
{
    /**
     * List announcements.
     *
     * Retrieves a list of announcements based on optional filters.
     *
     * @OA\Get(
     *     path="/api/announcements",
     *     summary="List announcements",
     *     tags={"Announcements"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter announcements by type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="Filter announcements by location",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="announcements", type="array", @OA\Items(ref="#/components/schemas/Announcement")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No announcements found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No announcements found."),
     *         )
     *     )
     * )
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

    /**
     * Create a new announcement.
     *
     * Allows an organizer to create a new announcement.
     *
     * @OA\Post(
     *     path="/api/announcements/create",
     *     summary="Create a new announcement",
     *     tags={"Announcements"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="required_skills", type="array", @OA\Items(type="string")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Announcement created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Announcement created successfully."),
     *             @OA\Property(property="announcement", ref="#/components/schemas/Announcement"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */

    public function store(AnnouncementRequest $request)
    {
      //  dd($AnnouncementRequest);
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
