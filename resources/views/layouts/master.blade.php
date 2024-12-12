<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>MKM Stamping Tabulasi</title>
        <link href="{{asset('assets/css/styles.css')}}" rel="stylesheet" />
        <link rel="icon" href="{{ asset('assets/img/mkm_logo.png') }}">

        <!-- PWA  -->
        <meta name="theme-color" content="rgba(0, 103, 127, 1)"/>
        <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
        <link rel="manifest" href="{{ asset('/manifest.json') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Resources -->
        <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/plugins/exporting.js"></script>


        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.css">

        <!-- Include jQuery (only once) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables JS -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

        <!-- DataTables Buttons JS -->
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

        <!-- Include Select2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

        <!-- Include Select2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

        <!-- Include FontAwesome and Feather Icons -->
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

        <!-- Include CKEditor -->
        <script src="https://cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>

        <!-- Include Chosen CSS and JS -->
        <link href="{{asset('chosen/chosen.min.css')}}" rel="stylesheet" />
        <script src="{{asset('chosen/chosen.jquery.min.js')}}"></script>

        <!-- Include Chart.js and Chart.js-adapter-date-fns -->
        <script src="{{ asset('plugins/chart.js/Chart.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    </head>

    <body class="nav-fixed sidenav-toggled">
        @include('layouts.includes._topbar')
            <div id="layoutSidenav">
                @include('layouts.includes._sidebar')
                    <div id="layoutSidenav_content">
                        @if (session('password'))
                        <script>
                            window.onload = function() {
                                alert("{{ session('password') }}");
                            };
                        </script>
                    @endif
                        @yield('content')
                        <footer class="footer-admin footer-light">
                            <div class="container-xl px-4">
                                <div class="row">
                                    <div class="col-md-6 small"></div>
                                    <div class="col-md-6 text-md-end small">
                                     Copyright PT Mitsubishi Krama Yudha Motors and Manufacturing&copy; 2023
                                    </div>
                                </div>
                            </div>
                        </footer>
                    </div>
            </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src={{asset('assets/js/scripts.js')}} ></script>
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
            (registration) => {
                console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                console.error(`Service worker registration failed: ${error}`);
            },
            );
        } else {
            console.error("Service workers are not supported.");
        }
        </script>
        <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('changePassword') }}">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                <label for="oldPassword" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                </div>
                <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                </div>
                <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
            </form>
        </div>
        </div>
    </div>
    <!-- Loader Spinner -->
<div id="loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<style>
    #loader {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("loader").style.display = "none"; // Hide loader once content is ready

        // Show loader on AJAX start
        $(document).on("ajaxStart", function () {
            document.getElementById("loader").style.display = "flex";
        });

        // Hide loader on AJAX stop
        $(document).on("ajaxStop", function () {
            document.getElementById("loader").style.display = "none";
        });
    });

    // Show loader when navigating away from the page
    window.addEventListener("beforeunload", function () {
        document.getElementById("loader").style.display = "flex";
    });
</script>

    </body>
</html>
