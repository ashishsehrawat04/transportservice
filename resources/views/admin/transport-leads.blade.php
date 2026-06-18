@extends('admin.Layout')

@section('content')

  @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Transport Leads</h4>
                    <a href="{{ route('admin.manage.transport_lead') }}" class="btn btn-primary btn-sm">
                        Add Transport Lead
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="multi-filter-select" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tracking</th>
                                    <th>User</th>
                                    <th>Item</th>
                                    <th>Route</th>
                                    <th>Distance</th>
                                    <th>Base Price</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Admin Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
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
            $('#multi-filter-select').DataTable({
                processing: true,
                serverSide: false,

                ajax: {
                    url: "{{ route('adminget.transport.leads') }}",

                    dataSrc: function (json) {
                        if (json.status) {
                            return json.data;
                        }

                        alert(json.message);
                        return [];
                    },

                    error: function(xhr) {
                        let response = xhr.responseJSON;

                        if (response && response.message) {
                            alert(response.message);
                        } else {
                            alert('Something went wrong');
                        }
                    }
                },

                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'tracking_number', defaultContent: '-' },
                    {
                        data: 'user',
                        render: function(user) {
                            return user ? user.name : '-';
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `${row.item_name}<br><small>${row.quantity} x ${row.item_type}</small>`;
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            let fromCity = row.city_route ? row.city_route.from_city : '-';
                            let toCity = row.city_route ? row.city_route.to_city : '-';
                            return `${fromCity} to ${toCity}`;
                        }
                    },
                    {
                        data: 'distance_km',
                        render: function(data) {
                            return parseFloat(data || 0).toFixed(2) + ' KM';
                        }
                    },
                    {
                        data: 'base_price',
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
                            return parseFloat(data || 0).toFixed(2);
                        }
                    },
                    {
                        data: 'admin_status',
                        render: function(data) {
                            return `<span class="badge bg-info">${data}</span>`;
                        }
                    },
                    {
                        data: 'payment_status',
                        render: function(data) {
                            return `<span class="badge bg-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "{{ url('admin/transport-leads/manage') }}/" + row.id;
                            let quoteUrl = editUrl + "?quote=1";
                            let invoiceUrl = "{{ url('admin/transport-leads') }}/" + row.id + "/invoice";
                            let deleteUrl = "{{ url('admin/transport-leads/delete') }}/" + row.id;
                            let invoiceButton = row.admin_status === 'delivered'
                                ? `<a href="${invoiceUrl}" class="btn btn-sm btn-success">Invoice PDF</a>`
                                : '';

                            return `
                                <a href="${editUrl}" class="btn btn-sm btn-primary">Edit</a>
                                <a href="${quoteUrl}" class="btn btn-sm btn-warning">Create Quote</a>
                                ${invoiceButton}
                                <a href="${deleteUrl}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            `;
                        }
                    }
                ],
                pageLength: 10
            });
        });
    </script>

@endsection
