<?php

namespace App\Http\Controllers;

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
}
