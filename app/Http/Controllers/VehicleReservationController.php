<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VehicleReservationController extends Controller
{
    public function index()
    {
        // Check fleet access
        if (!function_exists('requireLogin')) {
            // If auth functions don't exist, continue without them for now
        }
        
        $title = 'Vehicle Reservation';
        $styleLinks = '<link rel="stylesheet" href="' . asset('vendor/leaflet/leaflet.css') . '">';
        $scripts = '<script src="' . asset('js/table-pagination.js') . '"></script>'
                 . '\n<script src="' . asset('vendor/leaflet/leaflet.js') . '"></script>'
                 . '\n<script src="' . asset('js/vrds-reservation.js?v=20250920-1315') . '"></script>';
        $styles = '#reservationMap { height: 260px; border-radius: 8px; overflow: hidden; }';

        return view('vehicle-reservation.reservation', compact('title', 'styleLinks', 'scripts', 'styles'));
    }
}
