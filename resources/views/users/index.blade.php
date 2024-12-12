@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                {{-- <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                            Dropdown App Menu
                        </h1>
                        <div class="page-header-subtitle">Use this blank page as a starting point for creating new pages inside your project!</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">Optional page header content</div>
                </div> --}}
            </div>
        </div>
    </header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      {{-- <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>    </h1>
          </div>
        </div>
      </div><!-- /.container-fluid --> --}}
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of User</h3>
              </div>

              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-sm-12">
                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                            <i class="fas fa-plus-square"></i>
                          </button>

                          <!-- Modal -->
                          <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="modal-add-label">Add User</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('/user/store') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- Name Field -->
                                                <div class="form-group mb-2">
                                                    <label for="name">Full Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name" required>
                                                </div>

                                                <!-- Username Field -->
                                                <div class="form-group mb-2">
                                                    <label for="username">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
                                                </div>


                                            </div>
                                            <div class="col-md-6">
                                                <!-- Role Dropdown -->
                                                <div class="form-group mb-2">
                                                    <label for="role">Role</label>
                                                    <select name="role" id="role" class="form-control">
                                                        <option value="">- Please Select Role -</option>
                                                        @foreach ($dropdown as $role)
                                                            <option value="{{ $role->name_value }}">{{ $role->name_value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                 <!-- Email Field -->
                                                 <div class="form-group mb-2">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address" required>
                                                </div>

                                                <!-- Password Field -->
                                                <div class="form-group mb-2">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>

                              </div>
                            </div>
                          </div>


                          @include('partials.alert')
                </div>
                <div class="table-responsive">
                <table id="tableUser" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $no=1;
                    @endphp
                    @foreach ($user as $data)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $data->username }}</td>
                        <td>{{ $data->name }}</td>
                        <td>{{ $data->email }}</td>
                        <td>{{ $data->role }}</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime($data->last_login)) }}</td>
                        <td>
                            <button title="Edit User" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-update{{ $data->id }}">
                                <i class="fas fa-user-edit"></i>
                            </button>
                            @if ($data->is_active=='1')
                            <button title="Revoke Access" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-revoke{{ $data->id }}">
                                <i class="fas fa-user-lock"></i>
                            </button>
                            @else
                            <button title="Give Access" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-access{{ $data->id }}">
                                <i class="fas fa-user-check"></i>
                            </button>
                            @endif
                        </td>
                    </tr>

                    {{-- Modal Update --}}
                    <div class="modal fade" id="modal-update{{ $data->id }}" tabindex="-1" aria-labelledby="modal-update{{ $data->id }}-label" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal-update{{ $data->id }}-label">Edit User</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('/user/update/'.$data->id) }}" enctype="multipart/form-data" method="POST">
                                    @csrf
                                    @method('patch')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label for="name">Username</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}" readonly>
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ $data->email }}" readonly>
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label for="password">New Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label for="role">Role</label>
                                                    <select name="role" id="role" class="form-control">
                                                        <option value="">- Please Select Role -</option>
                                                        @foreach ($dropdown as $role)
                                                            <option value="{{ $role->name_value }}" {{ $data->role == $role->name_value ? 'selected' : '' }}>{{ $role->name_value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label for="plant">Plant</label>
                                                    <select name="plant" id="plant" class="form-control">
                                                        <option value="">- Please Select Plant -</option>
                                                        @foreach ($plants as $plant)
                                                            <option value="{{ $plant->name_value }}" {{ $data->plant == $plant->name_value ? 'selected' : '' }}>{{ $plant->name_value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label for="type">Type</label>
                                                    <select name="type" id="type" class="form-control">
                                                        <option value="">- Please Select Type -</option>
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type->name_value }}" {{ $data->type == $type->name_value ? 'selected' : '' }}>{{ $type->name_value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-dark btn-default" data-bs-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Update">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Modal Update --}}


                      {{-- Modal Access --}}
                      <div class="modal fade" id="modal-access{{ $data->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Give User Access</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('/user/access/'.$data->id) }}" enctype="multipart/form-data" method="GET">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group mb-2">
                                            Give Access to <label for="email">{{ $data->email }}</label>?
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Modal Access --}}


                     {{-- Modal Revoke --}}
                     <div class="modal fade" id="modal-revoke{{ $data->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Revoke User Access</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('/user/revoke/'.$data->id) }}" enctype="multipart/form-data" method="GET">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group mb-2">
                                            Are you sure you want to revoke <label for="email">{{ $data->email }}</label>?
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-dark btn-default" data-bs-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Revoke --}}



                    {{-- Modal Delete --}}
                    <div class="modal fade" id="modal-delete{{ $data->id }}" tabindex="-1" aria-labelledby="modal-delete{{ $data->id }}-label" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h4 class="modal-title" id="modal-delete{{ $data->id }}-label">Delete User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ url('/dropdown/delete/'.$data->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <div class="modal-body">
                                <div class="form-group mb-2">
                                Are you sure you want to delete <label for="Dropdown">{{ $data->name_value }}</label>?
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            </form>
                        </div>
                        </div>
                    </div>
                    {{-- Modal Delete --}}

                    @endforeach
                  </tbody>
                </table>
              </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>


</main>
<!-- For Datatables -->
<script>
    $(document).ready(function() {
      var table = $("#tableUser").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      });
    });
  </script>
@endsection
