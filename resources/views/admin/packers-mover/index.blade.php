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
                    <h4 class="card-title">Packers &amp; Movers</h4>
                    <a href="{{ route('admin.manage.packers_mover') }}" class="btn btn-primary btn-sm">
                        Add Branch
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive admin-table-scroll">
                        <table id="multi-filter-select" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>Price/KM/Kg</th>
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
                url: "{{ route('adminget.packers_movers') }}",
                type: 'GET',
                dataSrc: function (json) {
                    if (json.status && json.data) {
                        return json.data;
                    }
                    sweetNotify(json.message);
                    return [];
                },

                error: function(xhr, status, error) {
                    let message = 'Error loading packers & movers branches';
                    try {
                        let response = xhr.responseJSON;
                        if (response && response.message) {
                            message = response.message;
                        }
                    } catch(e) {}
                    sweetNotify(message);
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
                    data: 'name',
                    defaultContent: '-'
                },
                {
                    data: 'city',
                    defaultContent: '-'
                },
                {
                    data: 'price_per_km_per_kg',
                    defaultContent: '-',
                    render: function(data) {
                        if (data) return '₹ ' + parseFloat(data).toFixed(2);
                        return '-';
                    }
                },
                {
                    data: 'min_charge',
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
                            "{{ url('admin/packers-movers/manage') }}/" + row.id;

                        let deleteUrl =
                            "{{ url('admin/packers-movers/delete') }}/" + row.id;

                        return `
                            <div class="action-buttons">
                            <a href="${editUrl}"
                            class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="${deleteUrl}"
                            class="btn btn-sm btn-danger"
                            data-label="${row.name || 'this branch'}">
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
