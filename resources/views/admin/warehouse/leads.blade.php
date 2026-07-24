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
        <div class="col-sm-6 col-lg-2">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total</p>
                                <h4 class="card-title">{{ number_format($stats['total']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Pending</p>
                                <h4 class="card-title">{{ number_format($stats['pending']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">In Progress</p>
                                <h4 class="card-title">{{ number_format($stats['inProgress']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Delivered</p>
                                <h4 class="card-title">{{ number_format($stats['delivered']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Cancelled</p>
                                <h4 class="card-title">{{ number_format($stats['cancelled']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2">
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
                                <h4 class="card-title">{{ number_format($stats['revenue'], 0) }}</h4>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Warehouse Leads</h4>
                    <a href="{{ route('admin.manage.warehouse_lead') }}" class="btn btn-primary btn-sm">
                        Add Warehouse Lead
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive admin-table-scroll">
                        <table id="multi-filter-select" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tracking</th>
                                    <th>User</th>
                                    <th>Item</th>
                                    <th>Warehouse</th>
                                    <th>Storage Days</th>
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
                    url: "{{ route('adminget.warehouse_leads') }}",

                    dataSrc: function (json) {
                        if (json.status) {
                            return json.data;
                        }

                        sweetNotify(json.message);
                        return [];
                    },

                    error: function(xhr) {
                        let response = xhr.responseJSON;

                        if (response && response.message) {
                            sweetNotify(response.message);
                        } else {
                            sweetNotify('Something went wrong');
                        }
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
                            return `${row.item_name}<br><small>${row.quantity} x ${row.item_type || '-'}</small>`;
                        }
                    },
                    {
                        data: 'warehouse',
                        render: function(warehouse) {
                            return warehouse ? `${warehouse.name}<br><small>${warehouse.city}</small>` : '-';
                        }
                    },
                    {
                        data: 'storage_days',
                        render: function(data) {
                            return data ? data + (data > 1 ? ' Days' : ' Day') : '-';
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
                            const colors = {
                                pending: 'bg-warning', reviewed: 'bg-info', approved: 'bg-primary',
                                dispatched: 'bg-info', delivered: 'bg-success',
                                cancelled: 'bg-danger', rejected: 'bg-danger'
                            };
                            return `<span class="badge ${colors[data] || 'bg-secondary'}">${data}</span>`;
                        }
                    },
                    {
                        data: 'payment_status',
                        render: function(data) {
                            const colors = {
                                unpaid: 'bg-danger', partial: 'bg-warning',
                                paid: 'bg-success', refunded: 'bg-secondary'
                            };
                            return `<span class="badge ${colors[data] || 'bg-secondary'}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "{{ url('admin/warehouse-leads/manage') }}/" + row.id;
                            let quoteUrl = "{{ url('admin/warehouse-leads') }}/" + row.id + "/quote";
                            let quoteDownloadUrl = quoteUrl + "/download";
                            let invoiceUrl = "{{ url('admin/warehouse-leads') }}/" + row.id + "/invoice";
                            let deleteUrl = "{{ url('admin/warehouse-leads/delete') }}/" + row.id;
                            let invoiceButton = row.admin_status === 'delivered'
                                ? `<a href="${invoiceUrl}" class="btn btn-sm btn-success">Invoice PDF</a>`
                                : '';

                            return `
                                <div class="action-buttons">
                                <a href="${editUrl}" class="btn btn-sm btn-primary">Edit</a>
                                <a href="${quoteUrl}" class="btn btn-sm btn-info">Quote View</a>
                                <a href="${quoteDownloadUrl}" class="btn btn-sm btn-warning">Download PDF</a>
                                ${invoiceButton}
                                <a href="${deleteUrl}" class="btn btn-sm btn-danger" data-label="${row.tracking_number || 'this lead'}">Delete</a>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10
            });
        });
    </script>

@endsection
