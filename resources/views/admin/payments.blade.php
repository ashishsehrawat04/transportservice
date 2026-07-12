@extends('admin.Layout')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Revenue</p>
                            <h4 class="card-title">{{ number_format($paymentStats['totalRevenue'], 2) }}</h4>
                            <small>{{ number_format($paymentStats['todayRevenue'], 2) }} today</small>
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
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Payments</p>
                            <h4 class="card-title">{{ number_format($paymentStats['totalPayments']) }}</h4>
                            <small>{{ number_format($paymentStats['successPayments']) }} successful</small>
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
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pending</p>
                            <h4 class="card-title">{{ number_format($paymentStats['pendingPayments']) }}</h4>
                            <small>Awaiting confirmation</small>
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
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Refunded/Failed</p>
                            <h4 class="card-title">{{ number_format($paymentStats['refundedPayments'] + $paymentStats['failedPayments']) }}</h4>
                            <small>{{ number_format($paymentStats['refundedPayments']) }} refunded</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payment Section</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive admin-table-scroll">
                    <table id="payments-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>User</th>
                                <th>Lead</th>
                                <th>Route</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#payments-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('adminget.payments') }}",
                dataSrc: function (json) {
                    if (json.status) {
                        return json.data;
                    }

                    return [];
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    sweetNotify(response && response.message ? response.message : 'Something went wrong');
                }
            },
            scrollX: true,
            autoWidth: false,
            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'invoice_number', defaultContent: '-' },
                {
                    data: 'user',
                    render: function(user) {
                        if (!user) {
                            return '-';
                        }

                        return `${user.name}<br><small>${user.email || user.mobile || '-'}</small>`;
                    }
                },
                {
                    data: 'transport_lead',
                    render: function(lead) {
                        if (!lead) {
                            return '-';
                        }

                        return `${lead.tracking_number || '-'}<br><small>${lead.item_name || '-'}</small>`;
                    }
                },
                {
                    data: 'transport_lead',
                    render: function(lead) {
                        if (!lead) {
                            return '-';
                        }

                        let fromCity = lead.city_route ? lead.city_route.from_city : '-';
                        let toCity = lead.city_route ? lead.city_route.to_city : '-';

                        return `${fromCity} to ${toCity}`;
                    }
                },
                {
                    data: 'method',
                    render: function(data) {
                        return `<span class="badge bg-info">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const classes = {
                            success: 'bg-success',
                            pending: 'bg-warning',
                            failed: 'bg-danger',
                            refunded: 'bg-secondary',
                        };

                        return `<span class="badge ${classes[data] || 'bg-secondary'}">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'amount',
                    className: 'text-end',
                    render: function(data) {
                        return parseFloat(data || 0).toFixed(2);
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                }
            ],
            pageLength: 10,
            order: [[8, 'desc']]
        });
    });
</script>

@endsection
