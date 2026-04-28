<?php
include("core-functions.php");
include('include/security.php');
hms_require_role('admin', 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Search | Global Hospitals</title>
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
render_app_sidebar('pres', 'admin');

if(isset($_POST['pres_search_submit']))
{
	$contact = hms_clean_input($_POST['patient_contact']);
	$stmt = mysqli_prepare($con, "select * from prestb where contact=?");
    mysqli_stmt_bind_param($stmt, "s", $contact);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 0){
        echo "<script> alert('No prescriptions found for this patient contact!'); 
              window.location.href = 'admin-dashboard.php#list-pres';</script>";
    }
    else {
?>
    <main class="dashboard-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="font-weight-bold mb-1">Prescription Search Results</h2>
                    <p class="text-muted mb-0">Records for contact: <?php echo hms_esc($contact); ?></p>
                </div>
                <div class="text-right">
                    <a href="admin-dashboard.php#list-pres" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa fa-arrow-left mr-2"></i> Back to Registry
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Appt ID</th>
                                <th>Date</th>
                                <th>Disease</th>
                                <th>Prescription</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="font-weight-600"><?php echo hms_esc($row['fname'].' '.$row['lname']);?></td>
                                <td><?php echo hms_esc($row['doctor']);?></td>
                                <td>#<?php echo hms_esc($row['ID']);?></td>
                                <td><?php echo date('d M Y', strtotime($row['appdate']));?></td>
                                <td><span class="badge badge-info-soft px-3 rounded-pill"><?php echo hms_esc($row['disease']);?></span></td>
                                <td class="small text-muted"><?php echo hms_esc($row['prescription']);?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
<?php
    }
} else {
    header("Location: admin-dashboard.php#list-pres");
}
?>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
