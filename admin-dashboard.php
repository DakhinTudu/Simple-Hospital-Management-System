<!DOCTYPE html>
<?php 
include('include/config.php');
include('include/security.php');
hms_require_role('admin', 'index.php');
include('core-functions.php');

if(isset($_POST['docsub'])) {
  $doctor = hms_clean_input($_POST['doctor']);
  $dpassword = hms_clean_input($_POST['dpassword']);
  $demail = hms_clean_input($_POST['demail']);
  $spec = hms_clean_input($_POST['special']);
  $docFees = hms_clean_input($_POST['docFees']);
  $hashedPassword = hms_hash_password($dpassword);
  $stmt = mysqli_prepare($con, "insert into doctb(username,password,email,spec,docFees) values(?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "sssss", $doctor, $hashedPassword, $demail, $spec, $docFees);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
      hms_audit_log($con, 'doctor.added', 'doctor', $doctor, array('email' => $demail, 'spec' => $spec));
      echo "<script>alert('Doctor added successfully!'); window.location.href='admin-dashboard.php';</script>";
  }
}

if(isset($_POST['docsub1'])) {
  $demail = hms_clean_input($_POST['demail']);
  $doctorName = '';
  $lookupStmt = mysqli_prepare($con, "select username from doctb where email=? limit 1");
  mysqli_stmt_bind_param($lookupStmt, "s", $demail);
  mysqli_stmt_execute($lookupStmt);
  $lookupResult = mysqli_stmt_get_result($lookupStmt);
  if ($lookupResult && mysqli_num_rows($lookupResult) > 0) {
    $doctorRow = mysqli_fetch_assoc($lookupResult);
    $doctorName = $doctorRow['username'];
  }
  $stmt = mysqli_prepare($con, "delete from doctb where email=?");
  mysqli_stmt_bind_param($stmt, "s", $demail);
  $result = mysqli_stmt_execute($stmt);
  if($result) {
      hms_audit_log($con, 'doctor.deleted', 'doctor', $doctorName !== '' ? $doctorName : $demail, array('email' => $demail));
      echo "<script>alert('Doctor removed successfully!'); window.location.href='admin-dashboard.php';</script>";
  }
}

if(isset($_POST['admin_reset_submit'])) {
  $patientEmail = hms_clean_input($_POST['patient_email']);
  $newPassword = hms_clean_input($_POST['new_password']);
  if (hms_admin_reset_patient_password($con, $patientEmail, $newPassword)) {
    hms_audit_log($con, 'password.reset_by_admin', 'patient', $patientEmail);
    echo "<script>alert('Patient password reset successfully');</script>";
  }
}

if(isset($_POST['staff_add_submit'])) {
  $fullName = hms_clean_input($_POST['full_name']);
  $role = hms_clean_input($_POST['role']);
  $department = hms_clean_input($_POST['department']);
  $email = hms_clean_input($_POST['email']);
  $phone = hms_clean_input($_POST['phone']);
  $createdAt = date('Y-m-d H:i:s');
  $stmt = mysqli_prepare($con, "INSERT INTO stafftb(full_name,role,department,email,phone,status,created_at) VALUES(?,?,?,?,?,'active',?)");
  mysqli_stmt_bind_param($stmt, "ssssss", $fullName, $role, $department, $email, $phone, $createdAt);
  if (mysqli_stmt_execute($stmt)) {
    hms_audit_log($con, 'staff.added', 'staff', $fullName, array('role' => $role, 'department' => $department));
    echo "<script>alert('Staff member added successfully'); window.location.href='admin-dashboard.php#list-staff';</script>";
  }
}

if(isset($_POST['staff_deactivate_submit'])) {
  $staffId = (int)$_POST['staff_id'];
  $stmt = mysqli_prepare($con, "UPDATE stafftb SET status='inactive' WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $staffId);
  if (mysqli_stmt_execute($stmt)) {
    hms_audit_log($con, 'staff.deactivated', 'staff', (string)$staffId);
    echo "<script>alert('Staff member marked inactive'); window.location.href='admin-dashboard.php#list-staff';</script>";
  }
}

if(isset($_POST['lab_result_submit'])) {
  $labId = (int)$_POST['lab_id'];
  $resultValue = hms_clean_input($_POST['result_value']);
  $resultNotes = hms_clean_input($_POST['result_notes']);
  $status = hms_clean_input($_POST['status']);
  $reportedAt = date('Y-m-d H:i:s');
  $reportedBy = 'admin';
  $stmt = mysqli_prepare($con, "UPDATE labtesttb SET result_value=?, result_notes=?, status=?, reported_at=?, reported_by=? WHERE id=?");
  mysqli_stmt_bind_param($stmt, "sssssi", $resultValue, $resultNotes, $status, $reportedAt, $reportedBy, $labId);
  if (mysqli_stmt_execute($stmt)) {
    hms_audit_log($con, 'lab.result_updated', 'labtest', (string)$labId, array('status' => $status));
    echo "<script>alert('Lab report updated'); window.location.href='admin-dashboard.php#list-lab';</script>";
  }
}

$dailyStats = hms_get_daily_analytics($con);
$doctorLoadRows = hms_get_doctor_load($con, 8);
$trend14 = hms_get_appointments_trend($con, 14);
$specLoadRows = hms_get_specialization_load($con);
$slotUtilToday = hms_get_slot_utilization_today($con);
?>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Global Hospitals</title>
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
    <link rel="stylesheet" href="css/app-dashboard.css">
  </head>
  <body class="dashboard-body">
    <?php 
      include('include/app-header.php');
      include('include/app-sidebar.php');
      render_app_header('Staff Administrator');
      render_app_sidebar('dash', 'admin');
    ?>

    <main class="dashboard-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="font-weight-bold mb-1">Administrative Control Center</h2>
                    <p class="text-muted mb-0">Monitor hospital operations and manage medical staff.</p>
                </div>
                <div class="text-right">
                    <span class="badge badge-primary-soft p-3 px-4 rounded-pill shadow-sm">
                        <i class="fa fa-shield mr-2"></i> Authorized Access
                    </span>
                </div>
            </div>

            <div class="tab-content" id="nav-tabContent">
                <!-- Admin Overview -->
                <div class="tab-pane fade show active" id="list-dash" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-user-md fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Doctors</h4>
                                    <button onclick="document.getElementById('list-doc-list').click()" class="btn btn-primary btn-sm rounded-pill mt-3 px-4">Manage</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-users fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Patients</h4>
                                    <button onclick="document.getElementById('list-pat-list').click()" class="btn btn-primary btn-sm rounded-pill mt-3 px-4">View All</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-calendar fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Appointments</h4>
                                    <button onclick="document.getElementById('list-app-list').click()" class="btn btn-primary btn-sm rounded-pill mt-3 px-4">Analyze</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-line-chart fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Reports</h4>
                                    <button onclick="document.getElementById('list-reports-list').click()" class="btn btn-primary btn-sm rounded-pill mt-3 px-4">Analytics</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-8">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 font-weight-bold">Appointment Volume Trend</h6>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px;">
                                        <canvas id="adminTrendOverview"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 font-weight-bold">Daily Statistics</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-4">
                                            <div>
                                                <div class="small text-muted">Appointments Today</div>
                                                <div class="h4 font-weight-bold mb-0"><?php echo (int)$dailyStats['appointments_today']; ?></div>
                                            </div>
                                            <i class="fa fa-calendar-check-o fa-2x text-primary-soft"></i>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-4">
                                            <div>
                                                <div class="small text-muted">Cancellations Today</div>
                                                <div class="h4 font-weight-bold mb-0"><?php echo (int)$dailyStats['cancellations_today']; ?></div>
                                            </div>
                                            <i class="fa fa-times-circle fa-2x text-danger-soft"></i>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-4">
                                            <div>
                                                <div class="small text-muted">Available Slots</div>
                                                <div class="h4 font-weight-bold mb-0"><?php echo (int)$slotUtilToday['capacity'] - (int)$slotUtilToday['active_appointments']; ?></div>
                                            </div>
                                            <i class="fa fa-clock-o fa-2x text-success-soft"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Management -->
                <div class="tab-pane fade" id="list-doc" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Doctor Registry</h4>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 mr-2" type="button" data-toggle="collapse" data-target="#filterDoctors">
                                    <i class="fa fa-filter mr-1"></i> <span>Filter</span>
                                </button>
                                <a href="export-doctors.php" class="btn btn-outline-success btn-sm export-btn-mobile px-3 mr-3">
                                    <i class="fa fa-file-excel-o mr-1"></i> <span>Export Excel</span>
                                </a>
                                <div class="search-mobile-wrapper">
                                    <button class="search-toggle-btn mr-2"><i class="fa fa-search"></i></button>
                                    <form class="form-inline mb-0" method="post" action="doctorsearch.php">
                                        <?php echo hms_csrf_field(); ?>
                                        <div class="input-group">
                                            <input type="text" name="doctor_contact" placeholder="Search Email..." class="form-control form-control-sm rounded-left border-right-0">
                                            <div class="input-group-append">
                                                <button type="submit" name="doctor_search_submit" class="btn btn-primary btn-sm rounded-right">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Filter Collapse -->
                        <div class="collapse mb-4 px-4" id="filterDoctors">
                            <form method="get" action="admin-dashboard.php#list-doc" class="row align-items-end bg-light p-3 rounded shadow-sm">
                                <div class="col-md-9 mb-2">
                                    <label class="small font-weight-bold">Specialization</label>
                                    <select name="doc_spec" class="form-control form-control-sm">
                                        <option value="">All Specializations</option>
                                        <?php 
                                        $specs = mysqli_query($con, "SELECT DISTINCT spec FROM doctb");
                                        while($s = mysqli_fetch_assoc($specs)) {
                                            $sel = ($_GET['doc_spec'] ?? '') === $s['spec'] ? 'selected' : '';
                                            echo "<option value='".hms_esc($s['spec'])."' $sel>".hms_esc($s['spec'])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">Apply</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Specialization</th>
                                        <th>Contact Email</th>
                                        <th>Fees (₹)</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $filters = [
                                        'spec' => $_GET['doc_spec'] ?? ''
                                    ];
                                    $filterData = hms_build_filter_where($filters);
                                    $docPag = hms_get_pagination_data($con, "doctb", $filterData['where'], $filterData['params'], $filterData['types']);
                                    
                                    $query = "select * from doctb WHERE {$filterData['where']} LIMIT {$docPag['limit']} OFFSET {$docPag['offset']}";
                                    $stmt = mysqli_prepare($con, $query);
                                    if (!empty($filterData['params'])) {
                                        mysqli_stmt_bind_param($stmt, $filterData['types'], ...$filterData['params']);
                                    }
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Full Name" class="font-weight-600"><?php echo $row['username'];?></td>
                                        <td data-label="Specialization"><span class="badge badge-info-soft px-3 rounded-pill"><?php echo $row['spec'];?></span></td>
                                        <td data-label="Contact Email"><?php echo $row['email'];?></td>
                                        <td data-label="Fees (₹)" class="font-weight-bold text-primary"><?php echo $row['docFees'];?></td>
                                        <td data-label="Status" class="text-center"><span class="badge badge-success px-2 py-1 rounded-pill">Active</span></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($docPag['page'], $docPag['totalPages'], "list-doc"); ?>
                    </div>
                </div>

                <!-- Add Doctor -->
                <div class="tab-pane fade" id="list-settings" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Add New Medical Professional</h4>
                        </div>
                        <div class="card-body p-3 p-md-5">
                            <form method="post" action="admin-dashboard.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Full Name</label>
                                        <input type="text" class="form-control" name="doctor" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Email Address</label>
                                        <input type="email" class="form-control" name="demail" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Specialization</label>
                                        <select name="special" class="form-control custom-select" required>
                                            <option value="" disabled selected>Choose Specialty</option>
                                            <option value="General">General Physician</option>
                                            <option value="Cardiologist">Cardiologist</option>
                                            <option value="Neurologist">Neurologist</option>
                                            <option value="Pediatrician">Pediatrician</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Consultancy Fees (₹)</label>
                                        <input type="number" class="form-control" name="docFees" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Security Password</label>
                                        <input type="password" class="form-control" name="dpassword" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control" name="cdpassword" required>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="text-right">
                                    <button type="submit" name="docsub" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Patient List -->
                <div class="tab-pane fade" id="list-pat" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Patient Directory</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $patPag = hms_get_pagination_data($con, "patreg");
                                    $result = mysqli_query($con, "select * from patreg LIMIT {$patPag['limit']} OFFSET {$patPag['offset']}");
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Patient Name" class="font-weight-600"><?php echo $row['fname'].' '.$row['lname'];?></td>
                                        <td data-label="Gender"><?php echo ucfirst($row['gender']);?></td>
                                        <td data-label="Email"><?php echo $row['email'];?></td>
                                        <td data-label="Contact"><?php echo $row['contact'];?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($patPag['page'], $patPag['totalPages'], "list-pat"); ?>
                    </div>
                </div>
                
                <!-- Appointment Details -->
                <div class="tab-pane fade" id="list-app" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Appointment Details</h4>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 mr-2" type="button" data-toggle="collapse" data-target="#filterAppointments">
                                    <i class="fa fa-filter mr-1"></i> <span>Filter</span>
                                </button>
                                <a href="export-appointments.php" class="btn btn-outline-success btn-sm export-btn-mobile px-3 mr-3">
                                    <i class="fa fa-file-excel-o mr-1"></i> <span>Export Excel</span>
                                </a>
                                <div class="search-mobile-wrapper">
                                    <button class="search-toggle-btn mr-2"><i class="fa fa-search"></i></button>
                                    <form class="form-inline mb-0" method="post" action="appsearch.php">
                                        <div class="input-group">
                                            <input type="text" name="app_search" placeholder="Search..." class="form-control form-control-sm rounded-left border-right-0">
                                            <div class="input-group-append">
                                                <button type="submit" name="app_search_submit" class="btn btn-primary btn-sm rounded-right"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Collapse -->
                        <div class="collapse mb-4 px-4" id="filterAppointments">
                            <form method="get" action="admin-dashboard.php#list-app" class="row align-items-end bg-light p-3 rounded shadow-sm">
                                <div class="col-md-3 mb-2">
                                    <label class="small font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo hms_esc($_GET['start_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="small font-weight-bold">End Date</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo hms_esc($_GET['end_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="small font-weight-bold">Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="confirmed" <?php echo ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">Apply Filters</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Fees (₹)</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $filters = [
                                        'start_date' => $_GET['start_date'] ?? '',
                                        'end_date' => $_GET['end_date'] ?? '',
                                        'status' => $_GET['status'] ?? ''
                                    ];
                                    $filterData = hms_build_filter_where($filters);
                                    $appPag = hms_get_pagination_data($con, "appointmenttb", $filterData['where'], $filterData['params'], $filterData['types']);
                                    
                                    $query = "select * from appointmenttb WHERE {$filterData['where']} ORDER BY appdate DESC, apptime DESC LIMIT {$appPag['limit']} OFFSET {$appPag['offset']}";
                                    $stmt = mysqli_prepare($con, $query);
                                    if (!empty($filterData['params'])) {
                                        mysqli_stmt_bind_param($stmt, $filterData['types'], ...$filterData['params']);
                                    }
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="ID">#<?php echo $row['ID'];?></td>
                                        <td data-label="Patient" class="font-weight-600"><?php echo $row['fname'].' '.$row['lname'];?></td>
                                        <td data-label="Doctor"><?php echo $row['doctor'];?></td>
                                        <td data-label="Fees (₹)" class="text-primary font-weight-bold"><?php echo $row['docFees'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Time"><?php echo $row['apptime'];?></td>
                                        <td data-label="Status">
                                            <?php 
                                            if(($row['userStatus']==1) && ($row['doctorStatus']==1)) echo '<span class="badge badge-success px-2 rounded-pill">Confirmed</span>';
                                            else echo '<span class="badge badge-danger px-2 rounded-pill">Cancelled</span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($appPag['page'], $appPag['totalPages'], "list-app"); ?>
                    </div>
                </div>

                <!-- Prescription List -->
                <div class="tab-pane fade" id="list-pres" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Global Prescription Registry</h4>
                            <div class="d-flex align-items-center">
                                <a href="export-prescriptions.php" class="btn btn-outline-success btn-sm export-btn-mobile px-3 mr-3">
                                    <i class="fa fa-file-excel-o mr-1"></i> <span>Export Excel</span>
                                </a>
                                <div class="search-mobile-wrapper">
                                    <button class="search-toggle-btn mr-2"><i class="fa fa-search"></i></button>
                                    <form class="form-inline mb-0" method="post" action="pressearch.php">
                                        <div class="input-group">
                                            <input type="text" name="patient_contact" placeholder="Search Patient..." class="form-control form-control-sm rounded-left border-right-0">
                                            <div class="input-group-append">
                                                <button type="submit" name="pres_search_submit" class="btn btn-primary btn-sm rounded-right">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Disease</th>
                                        <th>Prescription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $presPag = hms_get_pagination_data($con, "prestb");
                                    $result = mysqli_query($con, "select * from prestb ORDER BY appdate DESC LIMIT {$presPag['limit']} OFFSET {$presPag['offset']}");
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Patient" class="font-weight-600"><?php echo $row['fname'].' '.$row['lname'];?></td>
                                        <td data-label="Doctor"><?php echo $row['doctor'];?></td>
                                        <td data-label="ID">#<?php echo $row['ID'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Disease"><span class="badge badge-info-soft px-3 rounded-pill"><?php echo $row['disease'];?></span></td>
                                        <td data-label="Prescription" class="small text-muted"><?php echo $row['prescription'];?></td>
                                        <td class="text-center">
                                            <a href="generate-prescription-pdf.php?ID=<?php echo $row['ID'];?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="fa fa-file-pdf-o"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($presPag['page'], $presPag['totalPages'], "list-pres"); ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-reports" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3"><h6>Specialization-wise Distribution</h6></div>
                                <div class="card-body"><div style="height:300px;"><canvas id="adminSpecLoadChart"></canvas></div></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3"><h6>Doctor Workload Metrics</h6></div>
                                <div class="card-body"><div style="height:300px;"><canvas id="adminDoctorLoadChart"></canvas></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="list-mes" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">User Queries & Feedback</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sender</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $mesPag = hms_get_pagination_data($con, "contact");
                                    $result = mysqli_query($con, "select * from contact ORDER BY contact DESC LIMIT {$mesPag['limit']} OFFSET {$mesPag['offset']}");
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td class="font-weight-600"><?php echo hms_esc($row['name']);?></td>
                                        <td><?php echo hms_esc($row['email']);?></td>
                                        <td class="small"><?php echo hms_esc($row['message']);?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($mesPag['page'], $mesPag['totalPages'], "list-mes"); ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-reset" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Security: Account Reset</h4>
                        </div>
                        <div class="card-body p-3 p-md-5">
                            <form method="post" action="admin-dashboard.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Patient Email Address</label>
                                        <input type="email" class="form-control" name="patient_email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>New Temporary Password</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                </div>
                                <button type="submit" name="admin_reset_submit" class="btn btn-danger px-5 rounded-pill shadow-sm">Reset Password</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-staff" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Staff Management</h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="admin-dashboard.php" class="mb-4">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-4 mb-3"><input type="text" name="full_name" class="form-control" placeholder="Full name" required></div>
                                    <div class="col-md-3 mb-3">
                                        <select name="role" class="form-control custom-select" required>
                                            <option value="" disabled selected>Select Role</option>
                                            <option value="doctor">Doctor</option>
                                            <option value="surgeon">Surgeon</option>
                                            <option value="nurse">Nurse</option>
                                            <option value="receptionist">Receptionist</option>
                                            <option value="lab_technician">Lab Technician</option>
                                            <option value="pharmacist">Pharmacist</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3"><input type="text" name="department" class="form-control" placeholder="Department"></div>
                                    <div class="col-md-2 mb-3"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
                                    <div class="col-md-5 mb-3"><input type="email" name="email" class="form-control" placeholder="Email"></div>
                                    <div class="col-md-3 mb-3"><button type="submit" name="staff_add_submit" class="btn btn-primary">Add Staff</button></div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-modern table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th><th>Name</th><th>Role</th><th>Department</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $staffPag = hms_get_pagination_data($con, "stafftb");
                                        $staffRows = mysqli_query($con, "SELECT * FROM stafftb ORDER BY id DESC LIMIT {$staffPag['limit']} OFFSET {$staffPag['offset']}");
                                        if($staffRows){ while($row = mysqli_fetch_assoc($staffRows)){ 
                                        ?>
                                        <tr>
                                            <td>#<?php echo (int)$row['id']; ?></td>
                                            <td><?php echo hms_esc($row['full_name']); ?></td>
                                            <td><?php echo hms_esc($row['role']); ?></td>
                                            <td><?php echo hms_esc($row['department']); ?></td>
                                            <td><?php echo hms_esc($row['email']); ?></td>
                                            <td><?php echo hms_esc($row['phone']); ?></td>
                                            <td><?php echo hms_esc($row['status']); ?></td>
                                            <td>
                                                <?php if($row['status'] === 'active'){ ?>
                                                <form method="post" action="admin-dashboard.php" style="display:inline;">
                                                    <?php echo hms_csrf_field(); ?>
                                                    <input type="hidden" name="staff_id" value="<?php echo (int)$row['id']; ?>">
                                                    <button type="submit" name="staff_deactivate_submit" class="btn btn-outline-danger btn-sm">Deactivate</button>
                                                </form>
                                                <?php } else { echo '-'; } ?>
                                            </td>
                                        </tr>
                                        <?php }} ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php echo hms_render_pagination($staffPag['page'], $staffPag['totalPages'], "list-staff"); ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-lab" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Lab Test Reports</h4>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 mr-2" type="button" data-toggle="collapse" data-target="#filterLab">
                                <i class="fa fa-filter mr-1"></i> <span>Filter</span>
                            </button>
                        </div>
                        
                        <!-- Lab Filter Collapse -->
                        <div class="collapse mb-4 px-4 mt-3" id="filterLab">
                            <form method="get" action="admin-dashboard.php#list-lab" class="row align-items-end bg-light p-3 rounded shadow-sm">
                                <div class="col-md-9 mb-2">
                                    <label class="small font-weight-bold">Status</label>
                                    <select name="lab_status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="ordered" <?php echo ($_GET['lab_status'] ?? '') === 'ordered' ? 'selected' : ''; ?>>Ordered</option>
                                        <option value="sample_collected" <?php echo ($_GET['lab_status'] ?? '') === 'sample_collected' ? 'selected' : ''; ?>>Sample Collected</option>
                                        <option value="completed" <?php echo ($_GET['lab_status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($_GET['lab_status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">Apply</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Patient ID</th><th>Doctor</th><th>Test</th><th>Status</th><th>Result</th><th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $filters = [
                                        'status' => $_GET['lab_status'] ?? ''
                                    ];
                                    $filterData = hms_build_filter_where($filters);
                                    $labPag = hms_get_pagination_data($con, "labtesttb", $filterData['where'], $filterData['params'], $filterData['types']);
                                    
                                    $query = "SELECT * FROM labtesttb WHERE {$filterData['where']} ORDER BY id DESC LIMIT {$labPag['limit']} OFFSET {$labPag['offset']}";
                                    $stmt = mysqli_prepare($con, $query);
                                    if (!empty($filterData['params'])) {
                                        mysqli_stmt_bind_param($stmt, $filterData['types'], ...$filterData['params']);
                                    }
                                    mysqli_stmt_execute($stmt);
                                    $labRows = mysqli_stmt_get_result($stmt);
                                    if($labRows){ while($row = mysqli_fetch_assoc($labRows)){ 
                                    ?>
                                    <tr>
                                        <td>#<?php echo (int)$row['id']; ?></td>
                                        <td><?php echo (int)$row['pid']; ?></td>
                                        <td><?php echo hms_esc($row['doctor']); ?></td>
                                        <td><?php echo hms_esc($row['test_name']); ?></td>
                                        <td><?php echo hms_esc($row['status']); ?></td>
                                        <td><?php echo hms_esc($row['result_value']); ?></td>
                                        <td>
                                            <form method="post" action="admin-dashboard.php" class="form-inline">
                                                <?php echo hms_csrf_field(); ?>
                                                <input type="hidden" name="lab_id" value="<?php echo (int)$row['id']; ?>">
                                                <input type="text" name="result_value" class="form-control form-control-sm mr-1" placeholder="Value" style="width:100px;" value="<?php echo hms_esc($row['result_value']); ?>">
                                                <input type="text" name="result_notes" class="form-control form-control-sm mr-1" placeholder="Notes" style="width:120px;" value="<?php echo hms_esc($row['result_notes']); ?>">
                                                <select name="status" class="form-control form-control-sm mr-1">
                                                    <option value="ordered" <?php echo $row['status']==='ordered'?'selected':''; ?>>ordered</option>
                                                    <option value="sample_collected" <?php echo $row['status']==='sample_collected'?'selected':''; ?>>sample_collected</option>
                                                    <option value="completed" <?php echo $row['status']==='completed'?'selected':''; ?>>completed</option>
                                                    <option value="cancelled" <?php echo $row['status']==='cancelled'?'selected':''; ?>>cancelled</option>
                                                </select>
                                                <button type="submit" name="lab_result_submit" class="btn btn-primary btn-sm">Save</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php }} ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo hms_render_pagination($labPag['page'], $labPag['totalPages'], "list-lab"); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      // Tab Persistence & Mobile UX Logic
      (function () {
        // Tab Persistence
        const hash = window.location.hash;
        if (hash) {
          $('.list-group-item[href="' + hash + '"]').tab('show');
        }

        // Mobile Search Toggle
        document.querySelectorAll('.search-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
                const input = this.parentElement.querySelector('input');
                if (this.parentElement.classList.contains('active') && input) {
                    input.focus();
                }
            });
        });

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-mobile-wrapper')) {
                document.querySelectorAll('.search-mobile-wrapper').forEach(w => w.classList.remove('active'));
            }
        });

        // Analytics Charts
        var trendData = <?php echo json_encode($trend14); ?>;
        var doctorLoadRows = <?php echo json_encode($doctorLoadRows); ?>;
        var specLoadRows = <?php echo json_encode($specLoadRows); ?>;

        const trendCtx = document.getElementById('adminTrendOverview');
        if (trendCtx) {
          new Chart(trendCtx, {
            type: 'line',
            data: {
              labels: trendData.labels,
              datasets: [{
                label: 'Total Appointments',
                data: trendData.bookings,
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30, 64, 175, 0.1)',
                fill: true,
                tension: 0.4
              }]
            },
            options: { responsive: true, maintainAspectRatio: false }
          });
        }

        const specCtx = document.getElementById('adminSpecLoadChart');
        if (specCtx) {
          new Chart(specCtx, {
            type: 'doughnut',
            data: {
              labels: specLoadRows.map(x => x.spec),
              datasets: [{
                data: specLoadRows.map(x => Number(x.total_appointments || 0)),
                backgroundColor: ['#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe']
              }]
            },
            options: { responsive: true, maintainAspectRatio: false }
          });
        }

        const loadCtx = document.getElementById('adminDoctorLoadChart');
        if (loadCtx) {
          new Chart(loadCtx, {
            type: 'bar',
            data: {
              labels: doctorLoadRows.map(x => x.doctor),
              datasets: [{
                label: 'Assigned Cases',
                data: doctorLoadRows.map(x => Number(x.total_appointments || 0)),
                backgroundColor: '#1e40af',
                borderRadius: 8
              }]
            },
            options: { responsive: true, maintainAspectRatio: false }
          });
        }
      })();
    </script>
  </body>
</html>
