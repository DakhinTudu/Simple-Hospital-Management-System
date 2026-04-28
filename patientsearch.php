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
    <title>Patient Details | Global Hospitals</title>
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
render_app_sidebar('pat', 'admin');

if(isset($_POST['patient_search_submit']))
{
	$contact=hms_clean_input($_POST['patient_contact']);
	$stmt = mysqli_prepare($con, "select fname,lname,email,contact from patreg where contact=? limit 1");
  mysqli_stmt_bind_param($stmt, "s", $contact);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row=mysqli_fetch_array($result);
  if(!$row){
    echo "<script> alert('No entries found! Please enter valid details'); 
          window.location.href = 'admin-dashboard.php#list-doc';</script>";
  }
  else {
    echo "
    <main class='dashboard-content'>
        <div class='container-fluid'>
            <div class='d-flex justify-content-between align-items-center mb-5'>
                <div>
                    <h2 class='font-weight-bold mb-1'>Patient Search Result</h2>
                    <p class='text-muted mb-0'>Details for contact: $contact</p>
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
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>".$row['fname']."</td>
                            <td>".$row['lname']."</td>
                            <td>".$row['email']."</td>
                            <td>".$row['contact']."</td>
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
    <script>
      document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.app-sidebar').classList.toggle('show');
      });
    </script>
</body>
</html>
