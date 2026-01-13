<?php

namespace App\Http\Controllers;

use App\Models\Information;

class StudentInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informations = Information::latest()->get();

        return view('students.information.index', compact('informations'));
    }
}
