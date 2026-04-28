<!DOCTYPE html>
<?php 
include('patient-auth.php');  
include('core-functions.php');
include('include/config.php');
include('include/security.php');
hms_require_role('patient', 'index.php');

$pid = $_SESSION['pid'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$fname = $_SESSION['fname'];
$gender = $_SESSION['gender'];
$lname = $_SESSION['lname'];
$contact = $_SESSION['contact'];

if(isset($_POST['app-submit'])) {
  $doctor = hms_clean_input($_POST['doctor']);
  $docFees = hms_clean_input($_POST['docFees']);
  $appdate = hms_clean_input($_POST['appdate']);
  $apptime = hms_clean_input($_POST['apptime']);
  $cur_date = date("Y-m-d");
  date_default_timezone_set('Asia/Kolkata');
  $cur_time = date("H:i:s");
  
  if(date("Y-m-d", strtotime($appdate)) >= $cur_date) {
    if((date("Y-m-d", strtotime($appdate)) == $cur_date && date("H:i:s", strtotime($apptime)) > $cur_time) || date("Y-m-d", strtotime($appdate)) > $cur_date) {
        if(hms_is_slot_free($con, $doctor, $appdate, $apptime)){
          $insert_stmt = mysqli_prepare($con, "insert into appointmenttb(pid,fname,lname,gender,email,contact,doctor,docFees,appdate,apptime,userStatus,doctorStatus) values(?,?,?,?,?,?,?,?,?,?,?,?)");
          $userStatus = '1';
          $doctorStatus = '1';
          mysqli_stmt_bind_param($insert_stmt, "isssssssssss", $pid, $fname, $lname, $gender, $email, $contact, $doctor, $docFees, $appdate, $apptime, $userStatus, $doctorStatus);
          $query = mysqli_stmt_execute($insert_stmt);

          if($query) {
            $appointmentId = (int)mysqli_insert_id($con);
            hms_book_slot($con, $doctor, $appdate, $apptime, $appointmentId);
            hms_audit_log($con, 'appointment.booked', 'appointment', (string)$appointmentId, array(
              'doctor' => $doctor,
              'appdate' => $appdate,
              'apptime' => $apptime
            ));
            echo "<script>alert('Your appointment successfully booked'); window.location.href='index.php';</script>";
          } else {
            echo "<script>alert('Unable to process your request. Please try again!');</script>";
          }
      } else {
        echo "<script>alert('We are sorry to inform that the doctor is not available at this time. Please choose another slot.');</script>";
      }
    } else {
      echo "<script>alert('Please select a time in the future!');</script>";
    }
  } else {
      echo "<script>alert('Please select a date in the future!');</script>";
  }
}

if(isset($_GET['cancel'])) {
    $cancelId = (int)$_GET['ID'];
    $slotLookupStmt = mysqli_prepare($con, "select doctor, appdate, apptime from appointmenttb where ID=? and pid=?");
    mysqli_stmt_bind_param($slotLookupStmt, "ii", $cancelId, $pid);
    mysqli_stmt_execute($slotLookupStmt);
    $slotLookupResult = mysqli_stmt_get_result($slotLookupStmt);
    $slotInfo = $slotLookupResult ? mysqli_fetch_assoc($slotLookupResult) : null;

    $cancelStmt = mysqli_prepare($con, "update appointmenttb set userStatus='0' where ID=? and pid=? and userStatus='1' and doctorStatus='1'");
    mysqli_stmt_bind_param($cancelStmt, "ii", $cancelId, $pid);
    $query = mysqli_stmt_execute($cancelStmt);
    if($query && mysqli_affected_rows($con) > 0) {
      if ($slotInfo) {
        hms_release_slot($con, $slotInfo['doctor'], $slotInfo['appdate'], $slotInfo['apptime']);
      }
      hms_audit_log($con, 'appointment.cancelled_by_patient', 'appointment', (string)$cancelId);
      echo "<script>alert('Your appointment successfully cancelled'); window.location.href='patient-dashboard.php';</script>";
    }
}

function generate_bill(){
  global $con;
  $pid = (int)$_SESSION['pid'];
  $billId = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;
  $output = '';
  $stmt = mysqli_prepare($con, "select p.pid,p.ID,p.fname,p.lname,p.doctor,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,a.docFees from prestb p inner join appointmenttb a on p.ID=a.ID where p.pid=? and p.ID=?");
  mysqli_stmt_bind_param($stmt, "ii", $pid, $billId);
  mysqli_stmt_execute($stmt);
  $query = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_array($query)){
        $output .= '
        <div style="font-family: Arial, sans-serif; background-color: #ffffff; margin: 0; padding: 0; color: #1e293b;">
            <div style="padding: 30px 50px; background-color: #ffffff;">
                <div style="text-align: center; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                    <h2 style="color: #102742; margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 2px;">Medical Bill & Summary</h2>
                    <p style="font-size: 10px; color: #64748b; margin-top: 5px;">ID: #'.$row["ID"].' | Issued: '.date('d M Y').'</p>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; background-color: #ffffff;">
                    <tr><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; width: 35%; color: #64748b; font-size: 10px; text-transform: uppercase;">Patient Name</td><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #102742;">'.$row["fname"].' '.$row["lname"].'</td></tr>
                    <tr><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase;">Consulting Doctor</td><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b;">Dr. '.$row["doctor"].'</td></tr>
                    <tr><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase;">Appointment Date</td><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b;">'.$row["appdate"].' at '.$row["apptime"].'</td></tr>
                    <tr><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase;">Diagnosis</td><td style="padding: 12px; border-bottom: 1px solid #f1f5f9; color: #b91c1c; font-weight: bold;">'.$row["disease"].'</td></tr>
                    <tr><td style="padding: 12px; font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase;">Prescription Summary</td><td style="padding: 12px; color: #1e293b; line-height: 1.6;">'.nl2br($row["prescription"]).'</td></tr>
                </table>

                <div style="margin-top: 20px; background-color: #f8fafc; padding: 30px; border-radius: 10px; text-align: right; border: 1px solid #e2e8f0;">
                    <span style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Consultancy Fees Paid</span><br>
                    <strong style="font-size: 32px; color: #102742;">₹'.$row["docFees"].'</strong>
                </div>

                <div style="margin-top: 50px; text-align: center; color: #94a3b8; font-size: 9px; line-height: 1.5; background-color: #ffffff;">
                    THIS IS A COMPUTER GENERATED BILL AND DOES NOT REQUIRE A PHYSICAL SIGNATURE.<br>
                    GLOBAL HOSPITALS IS COMMITTED TO YOUR WELLNESS.
                </div>
            </div>
        </div>';
    }
  return $output;
}

if(isset($_GET["generate_bill"])){
  require_once("TCPDF/tcpdf.php");
  $obj_pdf = new TCPDF('P',PDF_UNIT,PDF_PAGE_FORMAT,true,'UTF-8',false);
  $obj_pdf -> SetTitle("Hospital Bill - ".$pid);
  $obj_pdf -> SetMargins(5, 0, 5);
  $obj_pdf -> SetPrintHeader(false);
  $obj_pdf -> SetPrintFooter(false);
  $obj_pdf -> SetAutoPageBreak(TRUE, 5);
  $obj_pdf -> SetFont('helvetica','',11);
  $obj_pdf -> AddPage();
  
  // 1. Header with equal 5mm side margins
  $header_html = '
  <table style="width: 100%; background-color: #102742; color: #ffffff; padding: 20px 40px;">
      <tr>
          <td style="width: 60%; vertical-align: middle;">
              <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: bold;">GLOBAL HOSPITALS</h1>
              <p style="font-size: 10px; margin: 2px 0; opacity: 0.8; text-transform: uppercase;">Clinical Excellence</p>
          </td>
          <td style="width: 40%; text-align: right; vertical-align: middle; font-size: 9px; line-height: 1.5;">
              <strong style="color: #38bdf8;">Bhubaneswar Branch</strong><br>
              Plot No. 12, Patia, Odisha 751024<br>
              Ph: +91 674 2725 123
          </td>
      </tr>
  </table>';
  $obj_pdf->writeHTMLCell(200, 0, 5, 0, $header_html, 0, 1, true, true, 'L', true);

  // 2. Main Content
  $content = generate_bill();
  $obj_pdf->SetY(40);
  $obj_pdf->writeHTMLCell(200, 0, 5, 40, $content, 0, 1, true, true, 'L', true);

  // 3. Footer with equal 5mm side margins
  $footer_html = '
  <table style="width: 100%; background-color: #102742; color: #ffffff; padding: 20px 40px;">
      <tr>
          <td style="text-align: center; font-size: 9px; letter-spacing: 1px;">
              GLOBAL HOSPITALS BHUBANESWAR • PATIA, Odisha 751024 • WWW.GLOBALHOSPITALS.IN
          </td>
      </tr>
  </table>';
  $obj_pdf->writeHTMLCell(200, 0, 5, 277, $footer_html, 0, 1, true, true, 'L', true);

  ob_end_clean();
  $obj_pdf -> Output("bill_".$pid.".pdf",'I');
  exit();
}

$patientLabRows = mysqli_query($con, "SELECT * FROM labtesttb WHERE pid=" . (int)$pid . " ORDER BY id DESC");
?>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | Global Hospitals</title>
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
      render_app_header($username);
      render_app_sidebar('dash', 'patient');
    ?>

    <main class="dashboard-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="font-weight-bold mb-1">Welcome back, <?php echo $fname ?>!</h2>
                    <p class="text-muted mb-0">Manage your health appointments and medical records here.</p>
                </div>
                <div class="text-right">
                    <span class="badge badge-primary-soft p-3 px-4 rounded-pill shadow-sm">
                        <i class="fa fa-calendar mr-2"></i> <?php echo date('l, d F Y'); ?>
                    </span>
                </div>
            </div>

            <div class="tab-content" id="nav-tabContent">
                <!-- Overview Dashboard -->
                <div class="tab-pane fade show active" id="list-dash" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                            <i class="fa fa-calendar-plus-o fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <h4 class="StepTitle">Book Appointment</h4>
                                    <p class="text-muted small">Schedule a new visit with your preferred doctor.</p>
                                    <button onclick="document.getElementById('list-book-list').click()" class="btn btn-primary rounded-pill mt-3">Start Booking</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                            <i class="fa fa-history fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <h4 class="StepTitle">My Appointments</h4>
                                    <p class="text-muted small">View and manage your upcoming or past visits.</p>
                                    <button onclick="document.getElementById('list-history-list').click()" class="btn btn-primary rounded-pill mt-3">View History</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary-soft"></i>
                                            <i class="fa fa-file-text-o fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <h4 class="StepTitle">Prescriptions</h4>
                                    <p class="text-muted small">Access your medical prescriptions and bills.</p>
                                    <button onclick="document.getElementById('list-pres-list').click()" class="btn btn-primary rounded-pill mt-3">View Records</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Appointment Form -->
                <div class="tab-pane fade" id="list-home" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Schedule New Appointment</h4>
                            <p class="text-muted small mb-0">Fill in the details below to book your slot.</p>
                        </div>
                        <div class="card-body p-5">
                            <form method="post" action="patient-dashboard.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label>Select Specialization</label>
                                            <select name="spec" class="form-control custom-select" id="spec" required>
                                                <option value="" disabled selected>Choose Specialization</option>
                                                <?php display_specs(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label>Select Doctor</label>
                                            <select name="doctor" class="form-control custom-select" id="doctor" required>
                                                <option value="" disabled selected>Choose Doctor</option>
                                                <?php display_docs(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-4">
                                            <label>Consultancy Fees</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light border-right-0">₹</span>
                                                </div>
                                                <input type="text" name="docFees" id="docFees" class="form-control bg-light border-left-0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-4">
                                            <label>Preferred Date</label>
                                            <input type="date" name="appdate" id="appdate" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-4">
                                            <label>Available Time Slot</label>
                                            <select name="apptime" class="form-control custom-select" id="apptime" required>
                                                <option value="" disabled selected>Select Slot</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="text-right">
                                    <button type="submit" name="app-submit" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill">Confirm Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Appointment History -->
                <div class="tab-pane fade" id="app-hist" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 font-weight-bold text-primary">Appointment History</h4>
                            <span class="badge badge-primary-soft px-3 py-2">Total Visits: <?php 
                                $count_query = mysqli_query($con, "select count(*) as count from appointmenttb where pid=$pid");
                                $count_data = mysqli_fetch_assoc($count_query);
                                echo $count_data['count'];
                            ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Fees</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $query = "select ID,doctor,docFees,appdate,apptime,userStatus,doctorStatus from appointmenttb where pid = ? ORDER BY appdate DESC, apptime DESC;";
                                    $stmt = mysqli_prepare($con, $query);
                                    mysqli_stmt_bind_param($stmt, "i", $pid);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Doctor" class="font-weight-600"><?php echo $row['doctor'];?></td>
                                        <td data-label="Fees">₹<?php echo $row['docFees'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Time"><?php echo $row['apptime'];?></td>
                                        <td data-label="Status">
                                            <?php 
                                            if(($row['userStatus']==1) && ($row['doctorStatus']==1)) echo '<span class="badge badge-success px-3 py-2 rounded-pill">Confirmed</span>';
                                            elseif(($row['userStatus']==0) && ($row['doctorStatus']==1)) echo '<span class="badge badge-danger px-3 py-2 rounded-pill">Cancelled by You</span>';
                                            elseif(($row['userStatus']==1) && ($row['doctorStatus']==0)) echo '<span class="badge badge-warning px-3 py-2 rounded-pill">Cancelled by Doctor</span>';
                                            ?>
                                        </td>
                                        <td data-label="Action" class="text-center">
                                            <?php if(($row['userStatus']==1) && ($row['doctorStatus']==1)) { ?>
                                            <a href="patient-dashboard.php?ID=<?php echo $row['ID']?>&cancel=update" 
                                               onclick="return confirm('Are you sure you want to cancel this appointment?')" 
                                               class="btn btn-outline-danger btn-sm px-3 rounded-pill">Cancel</a>
                                            <?php } else { echo '-'; } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Prescription Records -->
                <div class="tab-pane fade" id="list-pres" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">Medical Records & Prescriptions</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Diagnosis</th>
                                        <th>Prescription</th>
                                        <th class="text-center">Billing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $query = "select p.doctor,p.ID,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,
                                              GROUP_CONCAT(CONCAT(e.medicine_name,' (',e.dosage,', ',e.duration,')') SEPARATOR '; ') AS emeds
                                              from prestb p
                                              left join eprescriptiontb e on e.appointment_id=p.ID and e.patient_id=p.pid
                                              where p.pid=?
                                              group by p.doctor,p.ID,p.appdate,p.apptime,p.disease,p.allergy,p.prescription
                                              ORDER BY p.appdate DESC;";
                                    $stmt = mysqli_prepare($con, $query);
                                    mysqli_stmt_bind_param($stmt, "i", $pid);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_array($result)){
                                    ?>
                                    <tr>
                                        <td data-label="Doctor" class="font-weight-600"><?php echo $row['doctor'];?></td>
                                        <td data-label="ID">#<?php echo $row['ID'];?></td>
                                        <td data-label="Date"><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                        <td data-label="Time"><?php echo $row['apptime'];?></td>
                                        <td data-label="Diagnosis"><span class="text-info"><?php echo $row['disease'];?></span></td>
                                        <td data-label="Prescription">
                                            <div class="small text-muted mb-1"><?php echo $row['prescription'];?></div>
                                            <?php if($row['emeds']) { ?>
                                            <div class="small text-primary"><i class="fa fa-medkit mr-1"></i> <?php echo hms_esc($row['emeds']); ?></div>
                                            <?php } ?>
                                        </td>
                                        <td data-label="Billing" class="text-center">
                                            <form method="get" action="patient-dashboard.php" target="_blank" class="mb-0">
                                                <input type="hidden" name="ID" value="<?php echo $row['ID']?>"/>
                                                <button type="submit" name="generate_bill" class="btn btn-success btn-sm px-4 rounded-pill shadow-sm">
                                                    <i class="fa fa-download mr-1"></i> Bill
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-lab" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-4">
                            <h4 class="mb-0 font-weight-bold text-primary">My Lab Reports</h4>
                            <p class="text-muted small mb-0">Track lab test orders and view completed results.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Doctor</th><th>Appt ID</th><th>Test</th><th>Status</th><th>Result</th><th>Notes</th><th>Reported At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($patientLabRows){ while($row = mysqli_fetch_assoc($patientLabRows)){ ?>
                                    <tr>
                                        <td>#<?php echo (int)$row['id']; ?></td>
                                        <td><?php echo hms_esc($row['doctor']); ?></td>
                                        <td><?php echo (int)$row['appointment_id']; ?></td>
                                        <td><?php echo hms_esc($row['test_name']); ?></td>
                                        <td><?php echo hms_esc($row['status']); ?></td>
                                        <td><?php echo hms_esc($row['result_value']); ?></td>
                                        <td><?php echo hms_esc($row['result_notes']); ?></td>
                                        <td><?php echo hms_esc($row['reported_at']); ?></td>
                                    </tr>
                                    <?php }} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
      // Appointment Slot Logic
      (function () {
        const specEl = document.getElementById('spec');
        const doctorEl = document.getElementById('doctor');
        const dateEl = document.getElementById('appdate');
        const timeEl = document.getElementById('apptime');
        const feesEl = document.getElementById('docFees');

        // Filter doctors by specialization
        specEl.addEventListener('change', function() {
            const spec = this.value;
            [...doctorEl.options].forEach(opt => {
                if(opt.value === "") return;
                const optSpec = opt.getAttribute('data-spec');
                opt.style.display = (optSpec === spec) ? 'block' : 'none';
            });
            doctorEl.value = "";
            feesEl.value = "";
            renderSlots([]);
        });

        // Update fees on doctor selection
        doctorEl.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            feesEl.value = selectedOpt.getAttribute('data-value') || "";
            loadSlots();
        });

        dateEl.addEventListener('change', loadSlots);

        function renderSlots(slots) {
          timeEl.innerHTML = '<option value="" disabled selected>Select Time</option>';
          if (!slots || !slots.length) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.textContent = (doctorEl.value && dateEl.value) ? 'No slots available' : 'Choose doctor & date';
            noOpt.disabled = true;
            timeEl.appendChild(noOpt);
            return;
          }
          slots.forEach(slot => {
            const opt = document.createElement('option');
            opt.value = slot.value;
            opt.textContent = slot.label;
            timeEl.appendChild(opt);
          });
        }

        function loadSlots() {
          if (!doctorEl.value || !dateEl.value) return;
          fetch('get-slots.php?doctor=' + encodeURIComponent(doctorEl.value) + '&appdate=' + encodeURIComponent(dateEl.value))
            .then(res => res.json())
            .then(data => renderSlots(data.slots || []))
            .catch(() => renderSlots([]));
        }
      })();
    </script>
  </body>
</html>
