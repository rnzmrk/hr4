<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AttendanceRecordController extends Controller
{
    /**
     * Display attendance record page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Fetch data from the API
            $response = Http::get('https://hr3.jetlougetravels-ph.com/api/monthly-attendance');
            
            if ($response->successful()) {
                $apiData = $response->json();
                
                // Debug: Log the raw API response
                \Log::info('API Response:', $apiData);
                
                if ($apiData['status'] === 'success' && isset($apiData['data'])) {
                    $attendanceData = collect($apiData['data'])->map(function($item) {
                        return [
                            'id' => $item['id'],
                            'employee_id' => 'EMP' . str_pad($item['employee_id'], 3, '0', STR_PAD_LEFT),
                            'employee_name' => $item['employee_name'],
                            'department' => $item['department'],
                            'month_start_date' => Carbon::parse($item['month_start_date'])->format('Y-m-d'),
                            'overtime_hours' => $item['overtime_hours'],
                            'present_days' => $item['present_days'],
                            'absent_days' => $item['absent_days'],
                            'total_hours' => $item['total_hours'],
                            'generated_at' => Carbon::parse($item['generated_at'])->format('Y-m-d H:i:s'),
                        ];
                    });
                    
                    // Debug: Log the processed data
                    \Log::info('Processed Attendance Data:', $attendanceData->toArray());
                } else {
                    $attendanceData = collect([]);
                    \Log::warning('API returned unsuccessful response or no data');
                }
            } else {
                $attendanceData = collect([]);
                \Log::error('API request failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            // Log error and use empty collection as fallback
            \Log::error('Failed to fetch attendance data: ' . $e->getMessage());
            $attendanceData = collect([]);
        }
        
        // Fallback: If no data from API, use sample data for testing
        if ($attendanceData->count() === 0) {
            \Log::info('Using fallback sample data');
            $attendanceData = collect([
                [
                    'id' => 1,
                    'employee_id' => 'EMP006',
                    'employee_name' => 'Jonnylito Duyanon',
                    'department' => 'Human Resources',
                    'month_start_date' => '2025-12-31',
                    'overtime_hours' => '0.00',
                    'present_days' => 4,
                    'absent_days' => 0,
                    'total_hours' => '0.00',
                    'generated_at' => '2026-01-30 15:17:48',
                ],
                [
                    'id' => 2,
                    'employee_id' => 'EMP007',
                    'employee_name' => 'Ceejay Encarnacion',
                    'department' => 'Human Resources',
                    'month_start_date' => '2025-12-31',
                    'overtime_hours' => '0.00',
                    'present_days' => 0,
                    'absent_days' => 0,
                    'total_hours' => '0.00',
                    'generated_at' => '2026-01-30 15:56:54',
                ]
            ]);
        }
        
        return view('hr4.payroll.attendance-record', compact('attendanceData'));
    }
}
  