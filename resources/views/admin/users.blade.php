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
            <div class="card-header">
                <h4 class="card-title">Users </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive admin-table-scroll">
                    <table id="multi-filter-select"class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
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

<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Name</strong>
                        <p class="mb-0" id="detailName">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email</strong>
                        <p class="mb-0" id="detailEmail">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Mobile</strong>
                        <p class="mb-0" id="detailMobile">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status</strong>
                        <p class="mb-0" id="detailStatus">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Role</strong>
                        <p class="mb-0" id="detailRole">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Wallet Balance</strong>
                        <p class="mb-0" id="detailWallet">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>City / State</strong>
                        <p class="mb-0" id="detailCityState">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Pincode</strong>
                        <p class="mb-0" id="detailPincode">-</p>
                    </div>
                    <div class="col-12">
                        <strong>Address</strong>
                        <p class="mb-0" id="detailAddress">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Demo file (production me mat use karna) -->
    <!-- <script src="{{ asset('assets/js/setting-demo2.js') }}"></script> -->
<script>
     $(document).ready(function () {

    $('#multi-filter-select').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('adminget.users') }}",

                dataSrc: function (json) {

                    if (json.status === true) {

                        console.log(json.message);

                        return json.data;
                    }

                    sweetNotify(json.message);

                    return [];
                },

                error: function(xhr, status, error) {

                    let response = xhr.responseJSON;

                    if (response && response.message) {
                        sweetNotify(response.message);
                    } else {
                        sweetNotify('Something went wrong');
                    }

                    console.error(xhr);
                }
            },
            scrollX: true,
            autoWidth: false,

            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: "name" },
                { data: "email" },
                { data: "mobile" },

                {
                    data: null,
                    render: function (data, type, row) {

                        let editUserUrl = "{{ url('admin/users/edit') }}";
                        let url = editUserUrl + "/" + row.slug;

                        let deleteUserUrl = "{{ url('admin/users/delete') }}";
                        let delete_url = deleteUserUrl + "/" + row.slug;

                        return `
                            <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-info viewUserBtn" data-id="${row.id}">
                                View
                            </button>

                            <a href="${url}" class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="${delete_url}"
                            class="btn btn-sm btn-danger"
                            data-label="${row.name || 'this user'}">
                                Delete
                            </a>
                            </div>
                        `;
                    }
                }
            ],

            pageLength: 5
        });

        $("#add-row").DataTable({
          pageLength: 5,
        });

        var action = '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });

        $(document).on('click', '.viewUserBtn', function () {
            let id = $(this).data('id');
            let userDetailsUrl = "{{ url('admin/get-users') }}" + "/" + id;

            $.ajax({
                url: userDetailsUrl,
                method: 'GET',
                success: function (json) {
                    if (json.status !== true) {
                        sweetNotify(json.message || 'User details not found');
                        return;
                    }

                    let user = json.data;
                    let cityState = [user.city, user.state, user.country].filter(Boolean).join(', ');
                    let address = [user.address_line_1, user.address_line_2, cityState].filter(Boolean).join(', ');

                    $('#detailName').text(user.name || '-');
                    $('#detailEmail').text(user.email || '-');
                    $('#detailMobile').text(user.mobile || '-');
                    $('#detailStatus').text(user.status || '-');
                    $('#detailRole').text(user.role || '-');
                    $('#detailWallet').text(user.wallet_balance ?? '-');
                    $('#detailCityState').text(cityState || '-');
                    $('#detailPincode').text(user.pincode || '-');
                    $('#detailAddress').text(address || '-');
                    $('#userDetailsModal').modal('show');
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    sweetNotify(response && response.message ? response.message : 'Something went wrong');
                }
            });
        });
    });
</script>

@endsection
