@extends('layouts.master')

@section('content')
<style>
    #lblGreetings {
        font-size: 1rem; /* Adjust the base font size as needed */
    }

    @media only screen and (max-width: 600px) {
        #lblGreetings {
            font-size: 1rem; /* Adjust the font size for smaller screens */
        }
    }

    .page-header .page-header-content {
        padding-top: 0rem;
        padding-bottom: 1rem;
    }

    .chartdiv {
        width: 100%;
        height: 500px;
        margin-bottom: 50px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    .btn-reset {
        background-color: #ff0000;
        color: white;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #cc0000;
    }
</style>
<main>
        {{-- <a class="btn btn-success" href="{{url('/api')}}">API</a>
        <a class="btn btn-success" href="{{url('/data')}}">L301</a>
        <a class="btn btn-success btn-sm" href="{{url('/L302')}}">L302</a>
        <a class="btn btn-success btn-sm" href="{{url('/L305')}}">L305</a>
        <a class="btn btn-success btn-sm" href="{{url('/L310')}}">L310</a>
        <a class="btn btn-success btn-sm" href="{{url('/L306')}}">L306</a> --}}
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div  class="container-fluid px-4">
            <div  class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-2">
                        <h1 class="page-header-title">
                            {{-- <div class="page-header-icon"><i data-feather="file"></i></div> --}}
                            <label id="lblGreetings"></label>
                            <a class="btn btn-success btn-sm" href="{{url('/api')}}">API</a>
                            <a class="btn btn-success btn-sm" href="{{url('/data')}}">L301</a>
                            <a class="btn btn-success btn-sm" href="{{url('/L302')}}">L302</a>
                            <a class="btn btn-success btn-sm" href="{{url('/L305')}}">L305</a>
                            <a class="btn btn-success btn-sm" href="{{url('/L310')}}">L310</a>
                            <a class="btn btn-success btn-sm" href="{{url('/L306')}}">L306</a>

                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-4 mt-n10">


<div class="card mb-4">
    <div class="card-header">
        <h1><i class="fas fa-table me-1"></i>
            Flow Process Data</h1>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped" style="border-collapse: collapse;">
                <thead>
                    <tr style="border: 2px solid black;">
                        <!-- Grouped Header -->
                        <th class="text-center" style="background-color: #9be69d;" colspan="5">L301 (Raw Material)</th>
                        <th class="text-center" style="background-color: #ffe680;" colspan="5">L302 (Prod Press)</th>
                        <th class="text-center" style="background-color: #9be69d;" colspan="5">L305 (Contac Area)</th>
                        <th class="text-center" style="background-color: #ffe680;" colspan="5">L310 (Prod Weld)</th>
                        <th class="text-center" style="background-color: #9be69d;" colspan="4">L306 (Contac Area)</th>
                    </tr>
                    <tr style="border: 2px solid black;">
                        <!-- Individual Columns -->
                        <th style="background-color: #9be69d;">Raw Material</th>
                        <th style="background-color: #9be69d;">Beg Stock</th>
                        <th style="background-color: #9be69d;">Received</th>
                        <th style="background-color: #9be69d;">Supply</th>
                        <th style="background-color: #9be69d;">Stock</th>

                        <th style="background-color: #ffe680;">Raw Material</th>
                        <th style="background-color: #ffe680;">Beg Stock</th>
                        <th style="background-color: #ffe680;">Received</th>
                        <th style="background-color: #ffe680;">Supply</th>
                        <th style="background-color: #ffe680;">Stock</th>

                        <th style="background-color: #9be69d;">After Press Material</th>
                        <th style="background-color: #9be69d;">Beg Stock</th>
                        <th style="background-color: #9be69d;">Received</th>
                        <th style="background-color: #9be69d;">Supply</th>
                        <th style="background-color: #9be69d;">Stock</th>

                        <th style="background-color: #ffe680;">After Press Material</th>
                        <th style="background-color: #ffe680;">Beg Stock</th>
                        <th style="background-color: #ffe680;">Received</th>
                        <th style="background-color: #ffe680;">Supply</th>
                        <th style="background-color: #ffe680;">Stock</th>

                        <th style="background-color: #9be69d;">Finish Good</th>
                        <th style="background-color: #9be69d;">Beg Stock</th>
                        <th style="background-color: #9be69d;">Received</th>
                        <th style="background-color: #9be69d;">Stock</th>
                    </tr>
                </thead>
            </table>
        </div>


    </div>

</div>
    </div>

</main>
<style>
    <style>
    /* Warna latar belakang sesuai lokasi */
    th, td {
        border: 1px solid #ccc; /* Menambahkan garis untuk pembatas */
    }

    /* Kolom akhir setiap lokasi dengan garis kanan tebal */
    th:last-child:nth-of-type(5n),
    td:last-child:nth-of-type(5n) {
        border-right: 5px solid black;
    }

    /* Baris dengan pembatas bawah */
    tr {
        border-bottom: 3px solid #ddd; /* Garis pemisah horizontal */
    }

    /* Warna lokasi L301 */
    th:nth-of-type(-n+5),
    td:nth-of-type(-n+5) {
        background-color: #9be69d;
    }

    /* Warna lokasi L302 */
    th:nth-of-type(n+6):nth-of-type(-n+10),
    td:nth-of-type(n+6):nth-of-type(-n+10) {
        background-color: #ffe680;
    }

    /* Warna lokasi L305 */
    th:nth-of-type(n+11):nth-of-type(-n+15),
    td:nth-of-type(n+11):nth-of-type(-n+15) {
        background-color: #9be69d;
    }

    /* Warna lokasi L310 */
    th:nth-of-type(n+16):nth-of-type(-n+20),
    td:nth-of-type(n+16):nth-of-type(-n+20) {
        background-color: #ffe680;
    }

    /* Warna lokasi L306 */
    th:nth-of-type(n+21):nth-of-type(-n+24),
    td:nth-of-type(n+21):nth-of-type(-n+24) {
        background-color: #9be69d
    }
</style>

</style>
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": false,
            "autoWidth": false,
            ajax: "{{ route('home.data') }}",
            columns: [
                { data: 'Raw Material (L301)', name: 'Raw Material (L301)' },
                { data: 'Beg Stock (L301)', name: 'Beg Stock (L301)' },
                { data: 'Received (L301)', name: 'Received (L301)' },
                { data: 'Supply (L301)', name: 'Supply (L301)' },
                { data: 'Stock (L301)', name: 'Stock (L301)' },
                { data: 'Raw Material (L302)', name: 'Raw Material (L302)' },
                { data: 'Beg Stock (L302)', name: 'Beg Stock (L302)' },
                { data: 'Received (L302)', name: 'Received (L302)' },
                { data: 'Supply (L302)', name: 'Supply (L302)' },
                { data: 'Stock (L302)', name: 'Stock (L302)' },
                { data: 'After Press Material (L305)', name: 'After Press Material (L305)' },
                { data: 'Beg Stock (L305)', name: 'Beg Stock (L305)' },
                { data: 'Received (L305)', name: 'Received (L305)' },
                { data: 'Supply (L305)', name: 'Supply (L305)' },
                { data: 'Stock (L305)', name: 'Stock (L305)' },
                { data: 'After Press Material (L310)', name: 'After Press Material (L310)' },
                { data: 'Beg Stock (L310)', name: 'Beg Stock (L310)' },
                { data: 'Received (L310)', name: 'Received (L310)' },
                { data: 'Supply (L310)', name: 'Supply (L310)' },
                { data: 'Stock (L310)', name: 'Stock (L310)' },
                { data: 'Finish Good (L306)', name: 'Finish Good (L306)' },
                { data: 'Beg Stock (L306)', name: 'Beg Stock (L306)' },
                { data: 'Received (L306)', name: 'Received (L306)' },
                { data: 'Stock (L306)', name: 'Stock (L306)' }
            ]
        });
    });
</script>

<script>
    var myDate = new Date();
    var hrs = myDate.getHours();

    var greet;

    if (hrs < 12)
        greet = 'Good Morning';
    else if (hrs >= 12 && hrs <= 17)
        greet = 'Good Afternoon';
    else if (hrs >= 17 && hrs <= 24)
        greet = 'Good Evening';

    document.getElementById('lblGreetings').innerHTML =
        '<b>' + greet + '</b> and welcome to MKM Stamping Tabulasi!';
</script>


@endsection
