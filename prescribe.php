<!DOCTYPE html>
<?php
include('doctor-auth.php');
include('include/security.php');
hms_require_role('doctor', 'index.php');
$pid='';
$ID='';
$appdate='';
$apptime='';
$fname = '';
$lname= '';
$doctor = $_SESSION['dname'];
if(isset($_GET['pid']) && isset($_GET['ID']) && ($_GET['appdate']) && isset($_GET['apptime']) && isset($_GET['fname']) && isset($_GET['lname'])) {
$pid = (int)$_GET['pid'];
  $ID = (int)$_GET['ID'];
  $fname = hms_clean_input($_GET['fname']);
  $lname = hms_clean_input($_GET['lname']);
  $appdate = hms_clean_input($_GET['appdate']);
  $apptime = hms_clean_input($_GET['apptime']);
}



if(isset($_POST['prescribe']) && isset($_POST['pid']) && isset($_POST['ID']) && isset($_POST['appdate']) && isset($_POST['apptime']) && isset($_POST['lname']) && isset($_POST['fname'])){
  $appdate = hms_clean_input($_POST['appdate']);
  $apptime = hms_clean_input($_POST['apptime']);
  $disease = hms_clean_input($_POST['disease']);
  $allergy = hms_clean_input($_POST['allergy']);
  $fname = hms_clean_input($_POST['fname']);
  $lname = hms_clean_input($_POST['lname']);
  $pid = (int)$_POST['pid'];
  $ID = (int)$_POST['ID'];
  $prescription = hms_clean_input($_POST['prescription']);
  $medicineName = isset($_POST['medicine_name']) ? hms_clean_input($_POST['medicine_name']) : '';
  $dosage = isset($_POST['dosage']) ? hms_clean_input($_POST['dosage']) : '';
  $duration = isset($_POST['duration']) ? hms_clean_input($_POST['duration']) : '';
  $instructions = isset($_POST['instructions']) ? hms_clean_input($_POST['instructions']) : '';
  
  $stmt = mysqli_prepare($con, "insert into prestb(doctor,pid,ID,fname,lname,appdate,apptime,disease,allergy,prescription) values (?,?,?,?,?,?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "siisssssss", $doctor, $pid, $ID, $fname, $lname, $appdate, $apptime, $disease, $allergy, $prescription);
  $query=mysqli_stmt_execute($stmt);
  if ($query && $medicineName !== '' && $dosage !== '' && $duration !== '') {
    $createdAt = date('Y-m-d H:i:s');
    $eStmt = mysqli_prepare($con, "insert into eprescriptiontb(appointment_id,patient_id,doctor,medicine_name,dosage,duration,instructions,created_at) values (?,?,?,?,?,?,?,?)");
    if ($eStmt) {
      mysqli_stmt_bind_param($eStmt, "iissssss", $ID, $pid, $doctor, $medicineName, $dosage, $duration, $instructions, $createdAt);
      mysqli_stmt_execute($eStmt);
    }
  }
    if($query)
    {
      hms_audit_log($con, 'prescription.created', 'appointment', (string)$ID, array(
        'patient_id' => $pid,
        'doctor' => $doctor
      ));
      echo "<script>alert('Prescribed successfully!');</script>";
    }
    else{
      echo "<script>alert('Unable to process your request. Try again!');</script>";
    }
  // else{
  //   echo "<script>alert('GET is not working!');</script>";
  // }initial
  // enga error?
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Prescription | Global Hospitals</title>
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
      render_app_sidebar('app', 'doctor');
    ?>

    <main class="dashboard-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="font-weight-bold mb-1">Create Prescription</h2>
                    <p class="text-muted mb-0">Patient: <?php echo $fname.' '.$lname; ?> | ID: <?php echo $pid; ?></p>
                </div>
                <div class="text-right d-flex align-items-center">
                    <a href="doctor-dashboard.php" class="btn btn-outline-primary rounded-pill px-4 mr-2">
                        <i class="fa fa-arrow-left mr-2"></i> Back to Panel
                    </a>
                </div>
            </div>

            <div class="public-card p-5 border-0 shadow-sm">
   <div class="container-fluid" style="margin-top:50px;">
    <h3 style = "margin-left: 40%;  padding-bottom: 20px; font-family: 'IBM Plex Sans', sans-serif;"> Welcome &nbsp<?php echo $doctor ?>
   </h3>

   <div class="tab-pane" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
        <form class="form-group" name="prescribeform" method="post" action="prescribe.php">
          <?php echo hms_csrf_field(); ?>
        
          <div class="row">
                  <div class="col-md-4"><label>Disease:</label></div>
                  <div class="col-md-8">
                  <!-- <input type="text" class="form-control" name="disease" required> -->
                  <textarea id="disease" class="form-control" rows ="5" name="disease" required></textarea>
                  </div><br><br><br>
                  
                  <div class="col-md-4"><label>Allergies:</label></div>
                  <div class="col-md-8">
                  <textarea id="allergy" class="form-control" rows ="5" name="allergy" required></textarea>
                  </div><br><br><br>
                  <div class="col-md-4"><label>Prescription:</label></div>
                  <div class="col-md-8">
                  <textarea id="prescription" class="form-control" rows ="10" name="prescription" required></textarea>
                  </div><br><br><br>
                  <div class="col-md-4"><label>Medicine Name:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control" name="medicine_name" required></div><br><br><br>
                  <div class="col-md-4"><label>Dosage:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control" name="dosage" placeholder="e.g. 1 tablet after meal" required></div><br><br><br>
                  <div class="col-md-4"><label>Duration:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control" name="duration" placeholder="e.g. 5 days" required></div><br><br><br>
                  <div class="col-md-4"><label>Instructions:</label></div>
                  <div class="col-md-8"><textarea class="form-control" rows="4" name="instructions"></textarea></div><br><br><br>
                  <input type="hidden" name="fname" value="<?php echo $fname ?>" />
                  <input type="hidden" name="lname" value="<?php echo $lname ?>" />
                  <input type="hidden" name="appdate" value="<?php echo $appdate ?>" />
                  <input type="hidden" name="apptime" value="<?php echo $apptime ?>" />
                  <input type="hidden" name="pid" value="<?php echo $pid ?>" />
                  <input type="hidden" name="ID" value="<?php echo $ID ?>" />
                  <br><br><br><br>
          <input type="submit" name="prescribe" value="Prescribe" class="btn btn-primary" style="margin-left: 40pc;">
          
        </form>
        <br>
      </div>
    </main>
      

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  </body>
</html>

