<!DOCTYPE html>
 <?php #include("func.php");?>
<html>
<head>
	<title>User Messages</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
<?php
include("newfunc.php");
include('include/security.php');
hms_require_role('admin', 'index.php');
if(isset($_POST['mes_search_submit']))
{
	$contact=hms_clean_input($_POST['mes_contact']);
	$stmt = mysqli_prepare($con, "select * from contact where contact=? limit 1");
  mysqli_stmt_bind_param($stmt, "s", $contact);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row=mysqli_fetch_array($result);
  if(!$row){
    echo "<script> alert('No entries found! Please enter valid details'); 
          window.location.href = 'admin-panel1.php#list-doc';</script>";
  } 
  else {
    echo "<div class='container-fluid' style='margin-top:50px;'>
    <div class='card'>
    <div class='card-body' style='background-color:#342ac1;color:#ffffff;'>
  <table class='table table-hover'>
    <thead>
      <tr>
        <th scope='col'>User Name</th>
        <th scope='col'>Email</th>
        <th scope='col'>Contact</th>
        <th scope='col'>Message</th>
      </tr>
    </thead>
    <tbody>";
  
    
          $name = $row['name'];
          $email = $row['email'];
          $contact = $row['contact'];
          $message = $row['message'];
          echo "<tr>
            <td>$name</td>
            <td>$email</td>
            <td>$contact</td>
            <td>$message</td>
          </tr>";
    
    echo "</tbody></table><center><a href='admin-panel1.php' class='btn btn-light'>Back to your Dashboard</a></div></center></div></div></div>";
  }
  }
	
?>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script> 
</body>
</html>