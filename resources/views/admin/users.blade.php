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
                <div class="table-responsive">
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

                    alert(json.message);

                    return [];
                },

                error: function(xhr, status, error) {

                    let response = xhr.responseJSON;

                    if (response && response.message) {
                        alert(response.message);
                    } else {
                        alert('Something went wrong');
                    }

                    console.error(xhr);
                }
            },

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
                            <a href="${url}" class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="${delete_url}"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">
                                Delete
                            </a>
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

        // Edit Button
        $(document).on('click', '.editBtn', function () {
            let id = $(this).data('id');
            alert("Edit ID: " + id);
        });

        // Delete Button
        $(document).on('click', '.deleteBtn', function () {
            let id = $(this).data('id');
            alert("Delete ID: " + id);
        });
    });
</script>

@endsection
