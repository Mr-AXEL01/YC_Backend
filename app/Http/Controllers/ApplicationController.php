<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Models\Announcement;
use App\Models\Application;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Application",
 *     title="Application",
 *     description="Application schema",
 *     @OA\Property(property="id", type="integer", description="ID of the application"),
 *     @OA\Property(property="volunteer_id", type="integer", description="ID of the volunteer"),
 *     @OA\Property(property="announcement_id", type="integer", description="ID of the announcement"),
 * )
 */
class ApplicationController extends Controller
{
    /**
     * Store a new application.
     *
     * Allows a volunteer to apply to a volunteer initiative.
     *
     * @OA\Post(
     *     path="/api/applications",
     *     summary="Store a new application",
     *     tags={"Applications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="announcement_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Application submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Application submitted successfully."),
     *             @OA\Property(property="application", type="object", ref="#/components/schemas/Application"),
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
    public function store(ApplicationRequest $request)
    {
        $validatedData = $request->validated();

        $application = new Application();
        $application->announcement_id = $validatedData['announcement_id'];
        $application->volunteer_id = auth()->id();
        $application->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ]);
    }

    /**
     * Get pending applications for the organizer.
     *
     * Retrieves pending applications for the authenticated organizer.
     *
     * @OA\Get(
     *     path="/api/applications",
     *     summary="Get pending applications for the organizer",
     *     tags={"Applications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="pending_applications", type="array", @OA\Items(ref="#/components/schemas/Application")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
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

    /**
     * Manage applications for the organizer.
     *
     * Allows the organizer to approve or reject applications from volunteers.
     *
     * @OA\Patch(
     *     path="/api/applications/{application}",
     *     summary="Manage applications for the organizer",
     *     tags={"Applications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="application",
     *         in="path",
     *         description="ID of the application",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="approved"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Application approved successfully."),
     *             @OA\Property(property="application", type="object", ref="#/components/schemas/Application"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
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
