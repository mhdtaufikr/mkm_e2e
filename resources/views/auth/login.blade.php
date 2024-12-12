<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>MKM Stamping Tabulasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/img/mkm_logo.png') }}">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .container-fluid {
            display: flex;
            height: 100%;
            width: 100%;
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }

        .picture-section {
            flex: 6; /* Adjust the flex ratio as needed */
            background-image: url("{{ asset('assets/img/MKM Tabulasi.jpg') }}");
            background-size: cover; /* Ensures the whole image is visible */
            background-repeat: no-repeat; /* Prevents tiling of the image */
            background-position: center; /* Centers the image within the section */
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }


        .picture-section h1 {
            font-size: 2.5rem;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        /* Right Section */
        .login-card {
            flex: 1.5; /* Adjust this value to reduce the login card space */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            padding: 30px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-card-content {
            max-width: 350px;
            width: 100%;
            text-align: center;
        }

        .login-card img {
            width: 150px; /* Adjust the size of the logo */
            margin-bottom: 25px;
        }

        .login-card h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-dark {
            width: 100%;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            gap: 0.5rem; /* Space between the icon and text */
        }

    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Left Section -->
        <div class="picture-section">

        </div>

            <!-- Right Section -->
            <div class="login-card">
                <div class="login-card-content">
                    <img src="{{ asset('assets/img/mkm_logo.png') }}" alt="SSQH Logo" style="height: 60px; margin-bottom: 20px;">
                    <h2>MKM Stamping Tabulasi</h2>
                    <small>Digital Tabulasi Data Stamping</small>
                    <!-- Alerts -->
                    @if (session('statusLogin'))
                    <div class="alert alert-warning" role="alert">
                        <strong>{{ session('statusLogin') }}</strong>
                    </div>
                    @elseif(session('statusLogout'))
                    <div class="alert alert-success" role="alert">
                        <strong>{{ session('statusLogout') }}</strong>
                    </div>
                    @endif

                    <!-- Login Form -->
                    <form action="{{ url('auth/login') }}" method="POST">
                        @csrf
                        <div class="mb-3 mt-3">

                            <input type="text" class="form-control" placeholder="Username" name="email" required />
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="Password" name="password" required />
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <a href="#" class="text-muted">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block mb-3">Log In</button>
                    </form>

                    <!-- Separator Line -->
                    <div class="text-center my-3">
                        <span class="text-muted">OR</span>
                        <hr />
                    </div>

                <!-- Social Login Buttons -->
            <div class="text-center">
                <form action="{{ url('auth/microsoft') }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 mb-2" aria-label="Continue with Microsoft">
                        <i class="fab fa-windows me-2"></i> Continue with Microsoft
                    </button>

                </form>
            </div>


                    <div class="footer text-center mt-4">
                        <p>&copy; 2023 PT Mitsubishi Krama Yudha Motors and Manufacturing</p>
                    </div>
                </div>
            </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
