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
                    <h4 class="card-title">City Routes </h4>
                    <a href="{{ route('admin.manage.city_route') }}" class="btn btn-primary btn-sm">
                        Add City Route
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive admin-table-scroll">
                        <table id="multi-filter-select" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>From City</th>
                                    <th>To City</th>
                                    <th>Fair Charges</th>
                                    <th>Rate/KM</th>
                                    <th>Rate/Volume</th>
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
                url: "{{ route('adminget.city.routes') }}",
                type: 'GET',
                dataSrc: function (json) {
                    console.log('API Response:', json);
                    if (json.status && json.data) {
                        console.log('Data loaded:', json.data);
                        return json.data;
                    }
                    console.error('API Error:', json.message);
                    alert('Error: ' + json.message);
                    return [];
                },

                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error, xhr);
                    let message = 'Error loading routes';
                    try {
                        let response = xhr.responseJSON;
                        if (response && response.message) {
                            message = response.message;
                        }
                    } catch(e) {}
                    console.error('Error details:', message);
                    alert('❌ ' + message);
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
                {
                    data: 'from_city',
                    defaultContent: '-'
                },
                {
                    data: 'to_city',
                    defaultContent: '-'
                },
                {
                    data: 'min_charge',
                    defaultContent: '-'
                },
                {
                    data: 'base_rate_per_weight',
                    defaultContent: '-',
                    render: function(data) {
                        if (data) return '₹ ' + parseFloat(data).toFixed(2);
                        return '-';
                    }
                },
                {
                    data: 'base_rate_per_volume',
                    defaultContent: '-',
                    render: function(data) {
                        if (data) return '₹ ' + parseFloat(data).toFixed(2);
                        return '-';
                    }
                },
                {
                    data: 'is_active',
                    render: function(data) {
                        if(data == 1){
                            return '<span class="badge bg-success">✓ Active</span>';
                        }
                        return '<span class="badge bg-danger">✗ Inactive</span>';
                    }
                },

                {
                    data: null,
                    orderable: false,
                    searchable: false,

                    render: function(data, type, row) {

                        let editUrl =
                            "{{ url('admin/city-routes/manage') }}/" + row.id;

                        let deleteUrl =
                            "{{ url('admin/city-routes/delete') }}/" + row.id;

                        return `
                            <div class="action-buttons">
                            <a href="${editUrl}"
                            class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="${deleteUrl}"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">
                                Delete
                            </a>
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
