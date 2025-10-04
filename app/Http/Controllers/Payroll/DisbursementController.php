<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Disbursement;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DisbursementController extends Controller
{
    public function index()
    {
        $disbursements = Disbursement::with('payroll.employee')
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (Disbursement $d) {
                $p = $d->payroll;
                $emp = $p?->employee?->name ?? '—';
                $period = $p ? ($p->period_start?->format('M d') . '–' . $p->period_end?->format('M d, Y')) : '';
                return [
                    'id' => $d->id,
                    'payroll' => trim($emp . ' - ' . $period),
                    'method' => $d->method,
                    'reference' => $d->reference,
                    'amount' => (float)$d->amount,
                    'status' => $d->status,
                    'paid_at' => $d->paid_at,
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

        return view('hr4.payroll.disbursements', [
            'disbursements' => $disbursements,
            'payrollOptions' => $payrollOptions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
            'method' => 'required|in:bank,cash,mobile',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);

        $dis = Disbursement::create([
            'payroll_id' => $data['payroll_id'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'amount' => $data['amount'],
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        // Optionally mark payroll Paid
        $payroll = Payroll::find($data['payroll_id']);
        if ($payroll && $payroll->status !== 'Paid') {
            $payroll->status = 'Paid';
            $payroll->save();
        }

        return redirect()->route('payroll.disbursements')->with('status', 'Disbursement created.');
    }
}
