<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = Payslip::with('payroll.employee')
            ->latest('issued_at')
            ->limit(100)
            ->get()
            ->map(function (Payslip $s) {
                $p = $s->payroll;
                $emp = $p?->employee?->name ?? '—';
                $period = $p ? ($p->period_start?->format('M d') . '–' . $p->period_end?->format('M d, Y')) : '';
                return [
                    'id' => $s->id,
                    'payroll' => trim($emp . ' - ' . $period),
                    'issued_at' => $s->issued_at,
                    'net' => (float)($p?->net_pay ?? 0),
                    'content' => $s->content ?? [],
                ];
            })
            ->toArray();

        $payrollOptions = Payroll::with('employee')
            ->latest('period_end')
            ->get()
            ->map(function (Payroll $p) {
                $label = sprintf('%s - %s–%s', $p->employee?->name ?? '—', $p->period_start?->format('M d'), $p->period_end?->format('M d, Y'));
                return ['id' => $p->id, 'label' => $label];
            });

        return view('hr4.payroll.payslips', [
            'payslips' => $payslips,
            'payrollOptions' => $payrollOptions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
            'issued_at' => 'required|date',
            // Allow either JSON string or array
            'content' => 'nullable',
        ]);

        $content = $data['content'] ?? [];
        if (is_string($content) && strlen(trim($content)) > 0) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            } else {
                // fallback: wrap raw string if invalid JSON
                $content = ['raw' => $content];
            }
        } elseif (!is_array($content)) {
            $content = [];
        }

        Payslip::create([
            'payroll_id' => $data['payroll_id'],
            'issued_at' => Carbon::parse($data['issued_at']),
            'content' => $content,
            'pdf_path' => null, 
        ]);

        return redirect()->route('payroll.payslips')->with('status', 'Payslip generated.');
    }
}
