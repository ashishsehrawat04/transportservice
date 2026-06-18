@extends('admin.Layout')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transport Quotes</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transport-quotes-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>Tracking</th>
                                <th>User</th>
                                <th>Item</th>
                                <th>Route</th>
                                <th>Subtotal</th>
                                <th>Tax</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th>Status</th>
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
        $('#transport-quotes-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('adminget.transport.quotes') }}",
                dataSrc: function (json) {
                    if (json.status) {
                        return json.data;
                    }

                    return [];
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    alert(response && response.message ? response.message : 'Something went wrong');
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'invoice_number', defaultContent: '-' },
                { data: 'tracking_number', defaultContent: '-' },
                {
                    data: null,
                    render: function(row) {
                        return `${row.customer_name || '-'}<br><small>${row.customer_email || row.customer_mobile || '-'}</small>`;
                    }
                },
                {
                    data: null,
                    render: function(row) {
                        return `${row.item_name || '-'}<br><small>${row.quantity || 1} x ${row.item_type || '-'}</small>`;
                    }
                },
                {
                    data: null,
                    render: function(row) {
                        return `${row.from_city || '-'} to ${row.to_city || '-'}`;
                    }
                },
                {
                    data: 'subtotal',
                    render: function(data) {
                        return parseFloat(data || 0).toFixed(2);
                    }
                },
                {
                    data: 'tax_amount',
                    render: function(data) {
                        return parseFloat(data || 0).toFixed(2);
                    }
                },
                {
                    data: 'discount_amount',
                    render: function(data) {
                        return parseFloat(data || 0).toFixed(2);
                    }
                },
                {
                    data: 'total_payment',
                    render: function(data) {
                        return `<strong>${parseFloat(data || 0).toFixed(2)}</strong>`;
                    }
                },
                {
                    data: null,
                    render: function(row) {
                        return `
                            <span class="badge bg-info">${row.admin_status || '-'}</span>
                            <br><small>${row.payment_status || '-'}</small>
                        `;
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
            order: [[11, 'desc']]
        });
    });
</script>

@endsection
