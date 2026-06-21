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
                    <h4 class="card-title">Transport Prices</h4>
                    <a href="{{ route('admin.manage.transport_price') }}" class="btn btn-primary btn-sm">
                        Add Transport Price
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive admin-table-scroll">
                        <table id="multi-filter-select" class="display table table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Type</th>
                                    <th>Calculate By</th>
                                    <th>Volume/CFT</th>
                                    <th>Distance/KM</th>
                                    <th>Min Charge</th>
                                    <th>Status</th>
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
                url: "{{ route('adminget.transport.prices') }}",

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
            scrollX: true,
            autoWidth: false,

            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'item_type' },
                {
                    data: 'calculation_type',
                    render: function(data) {
                        let type = data || 'distance';
                        return type.charAt(0).toUpperCase() + type.slice(1);
                    }
                },
                {
                    data: 'volume_rate_per_cft',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'distance_rate_per_km',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'min_charge',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'is_active',
                    render: function(data) {
                        if (data == 1) {
                            return '<span class="badge bg-success">Active</span>';
                        }
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let editUrl = "{{ url('admin/transport-prices/manage') }}/" + row.id;
                        let deleteUrl = "{{ url('admin/transport-prices/delete') }}/" + row.id;
                        return `
                            <div class="action-buttons">
                            <a href="${editUrl}" class="btn btn-sm btn-primary">Edit</a>
                            <a href="${deleteUrl}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
