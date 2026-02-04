@extends('layouts.app')

@section('page-title', 'Net Payout Details')
@section('page-subtitle', 'View detailed payout information')
@section('breadcrumbs', 'Payroll / Net Payout / Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    <i class="bi bi-wallet2 me-2"></i>Net Payout Details
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('net-payout.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Payout Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Payout Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ $netPayout->date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Salary:</strong></td>
                                    <td class="text-end">₱{{ number_format($netPayout->total_salary, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Net:</strong></td>
                                    <td class="text-end fw-bold">₱{{ number_format($netPayout->total_net, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">Total Deductions</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Total SSS:</strong></td>
                                    <td class="text-end">₱{{ number_format($netPayout->total_sss, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pag-IBIG:</strong></td>
                                    <td class="text-end">₱{{ number_format($netPayout->total_pagibig, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total PhilHealth:</strong></td>
                                    <td class="text-end">₱{{ number_format($netPayout->total_philhealth, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Income Tax:</strong></td>
                                    <td class="text-end">₱{{ number_format($netPayout->total_income_tax, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Net Payout Summary</h5>
                                    <h4 class="mb-0">₱{{ number_format($netPayout->total_net, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    
    .alert {
        border: 1px solid #28a745 !important;
        background-color: #d4edda !important;
    }
}
</style>
@endsection
