<?php

namespace App\Http\Controllers;

use App\Models\Cabang;

class StuUnitController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();
        $isValidUrl = $cabangs->contains(fn($c) => !empty($c->spreadsheet_url));

        return view('stu_unit.index', compact('cabangs', 'isValidUrl'));
    }
}
