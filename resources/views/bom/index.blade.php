@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-database"></i></div>
                            Master Bill of Material
                        </h1>
                        <div class="page-header-subtitle">Manage Bill of Material</div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List Bill of Material</h3>
                                </div>

                                @include('partials.alert')

                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-6">
                                            <a href="#" class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modalBom">
                                                <i class="fas fa-plus-square"></i> &nbsp;&nbsp; Input Bom
                                            </a>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalBom" tabindex="-1" aria-labelledby="modalBomLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form id="bomForm">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalBomLabel">Input BOM</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="dest" class="form-label">Destination</label>
                                                                <input type="text" class="form-control" id="dest" name="dest" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="model" class="form-label">Model</label>
                                                                <input type="text" class="form-control" id="model" name="model" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="bq" class="form-label">Batch Quantity (BQ)</label>
                                                                <input type="number" class="form-control" id="bq" name="bq" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="raw_material" class="form-label">Raw Material</label>
                                                                <input type="text" class="form-control" id="raw_material" name="raw_material" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="after_press" class="form-label">After Press</label>
                                                                <input type="text" class="form-control" id="after_press" name="after_press" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="after_welding" class="form-label">After Welding</label>
                                                                <input type="text" class="form-control" id="after_welding" name="after_welding" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                      {{--   <div class="col-sm-6 d-flex justify-content-end align-items-center">
                                            <!-- Legend section aligned to the right -->
                                            <div class="legend">
                                                <strong>Legend:</strong>
                                                <i class="fas fa-check" style='font-size: 20px; color: green;'></i> OK |
                                                <span style='font-size: 20px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span> Temporary |
                                                <i class="fas fa-times" style='font-size: 20px; color: red;'></i> Not Good

                                            </div>
                                        </div> --}}
                                    </div>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="tableBom" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Destination</th>
                                                        <th>Model</th>
                                                        <th>BQ</th>
                                                        <th>Raw Material</th>
                                                        <th>After Press</th>
                                                        <th>After Welding</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- DataTables will populate the rows here -->
                                                </tbody>
                                            </table>
                                        </div>

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


</main>

<script>
    $(document).ready(function() {
        $('#tableBom').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            autoWidth: false,
            ajax: "{{ url('/bom') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'dest', name: 'dest' },
                { data: 'model', name: 'model' },
                { data: 'bq', name: 'bq' },
                { data: 'raw_material', name: 'raw_material' },
                { data: 'after_press', name: 'after_press' },
                { data: 'after_welding', name: 'after_welding' },
            ]
        });
    });
</script>


<style>
    table td {
        white-space: nowrap;  /* Prevent text wrapping */
        overflow: hidden;     /* Hide overflow text */
        text-overflow: ellipsis; /* Show ellipsis for overflow */
    }
</style>


<script>
    $(document).ready(function () {
        $('#bomForm').on('submit', function (e) {
            e.preventDefault();

            let formData = {
                dest: $('#dest').val(),
                model: $('#model').val(),
                bq: $('#bq').val(),
                raw_material: $('#raw_material').val(),
                after_press: $('#after_press').val(),
                after_welding: $('#after_welding').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: "{{ route('bom.store') }}",
                type: "POST",
                data: formData,
                success: function (response) {
                    alert('BOM saved successfully!');
                    $('#modalBom').modal('hide');
                    $('#bomForm')[0].reset();
                    // Reload your DataTable or refresh the page if needed
                    location.reload();
                },
                error: function (xhr) {
                    alert('Error saving BOM. Please try again.');
                }
            });
        });
    });
</script>


<style>
    .modal-lg-x {
    max-width: 90%;
}
.modal-lg {
    max-width: 70%;
}
</style>


@endsection
