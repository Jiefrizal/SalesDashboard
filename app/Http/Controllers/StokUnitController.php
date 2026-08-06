<?php

namespace App\Http\Controllers;

use App\Models\Cabang;

class StokUnitController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();
        $isValidUrl = $cabangs->contains(fn($c) => !empty($c->stock_url) || !empty($c->spreadsheet_url));

        return view('stok_unit.index', compact('cabangs', 'isValidUrl'));
    }
}
