@extends('Admin.Layout')

@section('content')
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Users</p>
                            <h4 class="card-title">{{ number_format($dashboard['totalUsers']) }}</h4>
                            <small>{{ number_format($dashboard['pendingUsers']) }} pending approval</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-road"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Service Routes</p>
                            <h4 class="card-title">{{ number_format($dashboard['activeRoutes']) }}</h4>
                            <small>{{ number_format($dashboard['totalRoutes']) }} total routes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Shipment Leads</p>
                            <h4 class="card-title">{{ number_format($dashboard['totalLeads']) }}</h4>
                            <small>{{ number_format($dashboard['pendingLeads']) }} pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Revenue</p>
                            <h4 class="card-title">{{ number_format($dashboard['totalRevenue'], 2) }}</h4>
                            <small>{{ number_format($dashboard['todayRevenue'], 2) }} today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Shipments & Revenue</div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 330px">
                    <canvas id="shipmentRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-primary card-round">
            <div class="card-header">
                <div class="card-title">Lead Status</div>
                <div class="card-category">Current shipment workflow</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Approved</span>
                    <strong>{{ number_format($dashboard['approvedLeads']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Delivered</span>
                    <strong>{{ number_format($dashboard['deliveredLeads']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Paid Leads</span>
                    <strong>{{ number_format($dashboard['paidLeads']) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Unpaid Leads</span>
                    <strong>{{ number_format($dashboard['unpaidLeads']) }}</strong>
                </div>
            </div>
        </div>

        <div class="card card-round">
            <div class="card-body">
                <h5 class="mb-3">Quick Actions</h5>
                <a href="{{ route('admin.transport_leads') }}" class="btn btn-primary btn-sm mb-2 w-100">Manage Leads</a>
                <a href="{{ route('admin.city_routes') }}" class="btn btn-info btn-sm mb-2 w-100">Manage Routes</a>
                <a href="{{ route('admin.transport_prices') }}" class="btn btn-success btn-sm mb-2 w-100">Manage Prices</a>
                <a href="{{ route('admin.payments') }}" class="btn btn-secondary btn-sm w-100">Payment Section</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Recent Leads</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Tracking</th>
                                <th>User</th>
                                <th>Route</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads as $lead)
                                <tr>
                                    <td>{{ $lead->tracking_number }}</td>
                                    <td>{{ optional($lead->user)->name ?? '-' }}</td>
                                    <td>{{ optional($lead->cityRoute)->from_city ?? '-' }} to {{ optional($lead->cityRoute)->to_city ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($lead->admin_status) }}</span></td>
                                    <td class="text-end">{{ number_format($lead->total_payment, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No leads found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Payment History</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>User</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->invoice_number ?? '-' }}</td>
                                    <td>{{ optional($payment->user)->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                    <td><span class="badge bg-success">{{ ucfirst($payment->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No payments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Latest Users</div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($recentUsers as $user)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center border rounded p-3">
                                <div class="avatar me-3">
                                    <span class="avatar-title rounded-circle bg-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <div class="text-muted">{{ $user->email ?? $user->mobile ?? '-' }}</div>
                                    <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">No users found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.getElementById('shipmentRevenueChart');

        if (chartElement && window.Chart) {
            new Chart(chartElement, {
                type: 'line',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [
                        {
                            label: 'Leads',
                            data: @json($monthlyLeadCounts),
                            borderColor: '#177dff',
                            backgroundColor: 'rgba(23, 125, 255, 0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Revenue',
                            data: @json($monthlyRevenue),
                            borderColor: '#31ce36',
                            backgroundColor: 'rgba(49, 206, 54, 0.12)',
                            tension: 0.35,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
