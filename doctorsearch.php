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
    <title>Doctor Details | Global Hospitals</title>
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
render_app_sidebar('doc', 'admin');

if(isset($_POST['doctor_search_submit']))
{
	$contact=hms_clean_input($_POST['doctor_contact']);
  $stmt = mysqli_prepare($con, "select username,email,docFees from doctb where email=? limit 1");
  mysqli_stmt_bind_param($stmt, "s", $contact);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row=mysqli_fetch_array($result);
  if(!$row){
    echo "<script> alert('No entries found!'); 
          window.location.href = 'admin-dashboard.php#list-doc';</script>";
  }
  else {
    echo "
    <main class='dashboard-content'>
        <div class='container-fluid'>
            <div class='d-flex justify-content-between align-items-center mb-5'>
                <div>
                    <h2 class='font-weight-bold mb-1'>Doctor Search Result</h2>
                    <p class='text-muted mb-0'>Details for: $contact</p>
                </div>
                <div class='text-right'>
                    <a href='admin-dashboard.php' class='btn btn-outline-primary rounded-pill px-4'>
                        <i class='fa fa-arrow-left mr-2'></i> Back to Panel
                    </a>
                </div>
            </div>

            <div class='table-modern p-4'>
                <table class='table table-hover mb-0'>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Consultancy Fees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>".$row['username']."</td>
                            <td>".$row['email']."</td>
                            <td>".$row['docFees']."</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>";
  }
}
?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
