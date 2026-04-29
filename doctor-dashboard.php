<!DOCTYPE html>
<?php 
include('doctor-auth.php');
include('include/config.php');
include('include/security.php');
include('core-functions.php');
hms_require_role('doctor', 'index.php');
$doctor = $_SESSION['dname'];

if(isset($_GET['cancel'])) {
    $cancelId = (int)$_GET['ID'];
    $slotLookupStmt = mysqli_prepare($con, "select doctor, appdate, apptime from appointmenttb where ID=? and doctor=?");
    mysqli_stmt_bind_param($slotLookupStmt, "is", $cancelId, $doctor);
    mysqli_stmt_execute($slotLookupStmt);
    $slotLookupResult = mysqli_stmt_get_result($slotLookupStmt);
    $slotInfo = $slotLookupResult ? mysqli_fetch_assoc($slotLookupResult) : null;

    $stmt = mysqli_prepare($con, "update appointmenttb set doctorStatus='0' where ID=? and doctor=? and userStatus='1' and doctorStatus='1'");
    mysqli_stmt_bind_param($stmt, "is", $cancelId, $doctor);
    $query = mysqli_stmt_execute($stmt);
    if($query && mysqli_affected_rows($con) > 0) {
      if ($slotInfo) {
        hms_release_slot($con, $slotInfo['doctor'], $slotInfo['appdate'], $slotInfo['apptime']);
      }
      hms_audit_log($con, 'appointment.cancelled_by_doctor', 'appointment', (string)$cancelId);
      echo "<script>alert('Appointment successfully cancelled'); window.location.href='doctor-dashboard.php';</script>";
    }
}

if(isset($_POST['lab_order_submit'])) {
    $appointmentId = (int)$_POST['appointment_id'];
    $patientId = (int)$_POST['patient_id'];
    $testName = hms_clean_input($_POST['test_name']);
    $instructions = hms_clean_input($_POST['instructions']);
    $orderedAt = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($con, "INSERT INTO labtesttb(appointment_id,pid,doctor,test_name,instructions,status,ordered_at) VALUES(?,?,?,?,?,'ordered',?)");
    mysqli_stmt_bind_param($stmt, "iissss", $appointmentId, $patientId, $doctor, $testName, $instructions, $orderedAt);
    if (mysqli_stmt_execute($stmt)) {
        $labId = (int)mysqli_insert_id($con);
        hms_audit_log($con, 'lab.ordered', 'labtest', (string)$labId, array('appointment_id' => $appointmentId, 'pid' => $patientId));
        echo "<script>alert('Lab test ordered successfully'); window.location.href='doctor-dashboard.php#list-lab';</script>";
    }
}

$doctorLabRows = mysqli_query($con, "SELECT * FROM labtesttb WHERE doctor='" . mysqli_real_escape_string($con, $doctor) . "' ORDER BY id DESC");
?>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Portal | Global Hospitals</title>
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
      render_app_header($doctor);
      render_app_sidebar('dash', 'doctor');
    ?>

    <main class="dashboard-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="font-weight-bold mb-1">Doctor Portal</h2>
                    <p class="text-muted mb-0">Manage your clinical schedule and patient prescriptions.</p>
                </div>
                <div class="text-right d-flex align-items-center">
                    <div class="search-mobile-wrapper mr-3">
                        <button class="search-toggle-btn mr-2"><i class="fa fa-search"></i></button>
                        <form class="form-inline mb-0" method="post" action="search.php">
                            <?php echo hms_csrf_field(); ?>
                            <div class="input-group">
                                <input class="form-control form-control-sm rounded-left border-right-0" type="text" placeholder="Patient Contact..." name="contact">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-right" name="search_submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <span class="badge badge-primary-soft p-3 px-4 rounded-pill shadow-sm">
                        <i class="fa fa-clock-o mr-2"></i> <?php echo date('l, d M'); ?>
                    </span>
                </div>
            </div>

            <div class="tab-content" id="nav-tabContent">
                <!-- Doctor Dashboard Overview -->
                <div class="tab-pane fade show active" id="list-dash" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-calendar-check-o fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Appointments</h4>
                                    <p class="text-muted small">View your daily and weekly patient schedule.</p>
                                    <button onclick="document.getElementById('list-app-list').click()" class="btn btn-primary rounded-pill mt-3 px-4">View Schedule</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body">
                                    <span class="fa-stack fa-2x mb-3">
                                        <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                        <i class="fa fa-medkit fa-stack-1x"></i>
                                    </span>
                                    <h4 class="StepTitle">Prescriptions</h4>
                                    <p class="text-muted small">Access and manage patient medical records.</p>
                                    <button onclick="document.getElementById('list-pres-list').click()" class="btn btn-primary rounded-pill mt-3 px-4">Medical Log</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment List -->
                <div class="tab-pane fade" id="list-app" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Patient Appointment List</h4>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 mr-2" type="button" data-toggle="collapse" data-target="#filterDoctorApps">
                                <i class="fa fa-filter mr-1"></i> <span>Filter</span>
                            </button>
                        </div>
                        
                        <!-- Filter Collapse -->
                        <div class="collapse mb-4 px-4 mt-3" id="filterDoctorApps">
                            <form method="get" action="doctor-dashboard.php#list-app" class="row align-items-end bg-light p-3 rounded shadow-sm">
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo hms_esc($_GET['start_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">End Date</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo hms_esc($_GET['end_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">Apply Filters</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>ID</th>
                                        <th>Gender</th>
                                        <th>Contact</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $filters = [
                                        'doctor' => $doctor,
                                        'start_date' => $_GET['start_date'] ?? '',
                                        'end_date' => $_GET['end_date'] ?? ''
                                    ];
                                    $filterData = hms_build_filter_where($filters);
                                    $appPag = hms_get_pagination_data($con, "appointmenttb", $filterData['where'], $filterData['params'], $filterData['types']);
                                    
                                    $query = "select pid,ID,fname,lname,gender,email,contact,appdate,apptime,userStatus,doctorStatus,
                                               (SELECT COUNT(*) FROM prestb WHERE prestb.ID = appointmenttb.ID AND prestb.doctor = appointmenttb.doctor) AS is_prescribed
                                               from appointmenttb where {$filterData['where']} ORDER BY appdate DESC, apptime DESC LIMIT {$appPag['limit']} OFFSET {$appPag['offset']};";
                                    $stmt = mysqli_prepare($con, $query);
                                    if (!empty($filterData['params'])) {
                                        mysqli_stmt_bind_param($stmt, $filterData['types'], ...$filterData['params']);
                                    }
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Patient" class="font-weight-600"><?php echo $row['fname'].' '.$row['lname'];?></td>
                                        <td data-label="ID">#<?php echo $row['ID'];?></td>
                                        <td data-label="Gender"><?php echo ucfirst($row['gender']);?></td>
                                        <td data-label="Contact"><?php echo $row['contact'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Time"><?php echo $row['apptime'];?></td>
                                        <td data-label="Status">
                                            <?php
                                             if(($row['userStatus']==1) && ($row['doctorStatus']==1)) {
                                                 if ($row['is_prescribed'] > 0)
                                                     echo '<span class="badge badge-info px-2 rounded-pill">Prescribed</span>';
                                                 else
                                                     echo '<span class="badge badge-success px-2 rounded-pill">Confirmed</span>';
                                             } else {
                                                 echo '<span class="badge badge-danger px-2 rounded-pill">Cancelled</span>';
                                             }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if(($row['userStatus']==1) && ($row['doctorStatus']==1)) { ?>
                                            <div class="btn-group">
                                                <?php if ($row['is_prescribed'] > 0) { ?>
                                                     <span class="badge badge-info-soft px-3 py-2 rounded-pill mr-2">
                                                         <i class="fa fa-check-circle mr-1"></i> Prescribed
                                                     </span>
                                                     <a href="generate-prescription-pdf.php?ID=<?php echo $row['ID'];?>" target="_blank" 
                                                        class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                                                         <i class="fa fa-file-pdf-o mr-1"></i> PDF
                                                     </a>
                                                <?php } else { ?>
                                                <a href="doctor-dashboard.php?ID=<?php echo $row['ID']?>&cancel=update" 
                                                   onclick="return confirm('Are you sure you want to cancel this appointment?')" 
                                                   class="btn btn-outline-danger btn-sm px-3 rounded-pill mr-2">Cancel</a>
                                                <a href="prescribe.php?pid=<?php echo $row['pid'];?>&ID=<?php echo $row['ID'];?>&fname=<?php echo $row['fname'];?>&lname=<?php echo $row['lname'];?>&appdate=<?php echo $row['appdate'];?>&apptime=<?php echo $row['apptime'];?>" 
                                                   class="btn btn-success btn-sm px-3 rounded-pill">Prescribe</a>
                                                <?php } ?>
                                            </div>
                                            <?php } else { echo '-'; } ?>
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
                            <h4 class="mb-0 font-weight-bold text-primary">Issued Prescriptions</h4>
                            <a href="export-prescriptions.php" class="btn btn-outline-success btn-sm export-btn-mobile px-4">
                                <i class="fa fa-file-excel-o mr-2"></i> <span>Download clinical log</span>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Appt ID</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Diagnosis</th>
                                        <th>Prescription Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $presPag = hms_get_pagination_data($con, "prestb", "doctor=?", [$doctor], "s");
                                    $query = "select * from prestb where doctor=? ORDER BY appdate DESC LIMIT {$presPag['limit']} OFFSET {$presPag['offset']};";
                                    $stmt = mysqli_prepare($con, $query);
                                    mysqli_stmt_bind_param($stmt, "s", $doctor);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Patient Name" class="font-weight-600"><?php echo $row['fname'].' '.$row['lname'];?></td>
                                        <td data-label="Appt ID">#<?php echo $row['ID'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Time"><?php echo $row['apptime'];?></td>
                                        <td data-label="Diagnosis"><span class="text-info"><?php echo $row['disease'];?></span></td>
                                        <td data-label="Prescription Details" class="small"><?php echo $row['prescription'];?></td>
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

                <div class="tab-pane fade" id="list-lab" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Order Lab Test</h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="doctor-dashboard.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-2 mb-3"><input type="number" name="appointment_id" class="form-control" placeholder="Appt ID" required></div>
                                    <div class="col-md-2 mb-3"><input type="number" name="patient_id" class="form-control" placeholder="Patient ID" required></div>
                                    <div class="col-md-4 mb-3"><input type="text" name="test_name" class="form-control" placeholder="Test name (CBC, LFT, etc.)" required></div>
                                    <div class="col-md-4 mb-3"><input type="text" name="instructions" class="form-control" placeholder="Instructions (fasting, sample, etc.)"></div>
                                    <div class="col-md-3 mb-3"><button type="submit" name="lab_order_submit" class="btn btn-primary">Order Test</button></div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">My Lab Test Orders</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Patient ID</th><th>Appt ID</th><th>Test</th><th>Status</th><th>Result</th><th>Ordered At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $labPag = hms_get_pagination_data($con, "labtesttb", "doctor=?", [$doctor], "s");
                                    $doctorLabRows = mysqli_query($con, "SELECT * FROM labtesttb WHERE doctor='" . mysqli_real_escape_string($con, $doctor) . "' ORDER BY id DESC LIMIT {$labPag['limit']} OFFSET {$labPag['offset']}");
                                    if($doctorLabRows){ while($row = mysqli_fetch_assoc($doctorLabRows)){ 
                                    ?>
                                    <tr>
                                        <td>#<?php echo (int)$row['id']; ?></td>
                                        <td><?php echo (int)$row['pid']; ?></td>
                                        <td><?php echo (int)$row['appointment_id']; ?></td>
                                        <td><?php echo hms_esc($row['test_name']); ?></td>
                                        <td><?php echo hms_esc($row['status']); ?></td>
                                        <td><?php echo hms_esc($row['result_value']); ?></td>
                                        <td><?php echo hms_esc($row['ordered_at']); ?></td>
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
    <script>
      // Tab Persistence & Mobile UX Logic
      (function () {
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
      })();
    </script>
  </body>
</html>
