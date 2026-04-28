<?php
session_start();
include('include/config.php');
include('include/security.php');
if(isset($_POST['patsub'])){
	$email=hms_clean_input($_POST['email']);
	$password=hms_clean_input($_POST['password2']);
  if (!hms_is_valid_email($email)) {
    echo("<script>alert('Please enter a valid email address.');
          window.location.href = 'index.php';</script>");
    exit();
  }
  $stmt = mysqli_prepare($con, "select pid,fname,lname,gender,contact,email,password from patreg where email=? limit 1");
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
	if($result && mysqli_num_rows($result)===1)
	{
    $row = mysqli_fetch_assoc($result);
    if (hms_verify_password($password, $row['password'])) {
      if (!hms_is_password_hashed($row['password'])) {
        $newHash = hms_hash_password($password);
        $updateStmt = mysqli_prepare($con, "update patreg set password=?, cpassword=? where pid=?");
        $pid = (int)$row['pid'];
        mysqli_stmt_bind_param($updateStmt, "ssi", $newHash, $newHash, $pid);
        mysqli_stmt_execute($updateStmt);
      }
      hms_login_user('patient', array(
        'pid' => (int)$row['pid'],
        'username' => $row['fname']." ".$row['lname'],
        'fname' => $row['fname'],
        'lname' => $row['lname'],
        'gender' => $row['gender'],
        'contact' => $row['contact'],
        'email' => $row['email']
      ));
  		header("Location: patient-dashboard.php");
      exit();
    }
	}
  else {
    echo("<script>alert('Invalid Username or Password. Try Again!');
          window.location.href = 'index.php';</script>");
    // header("Location:error.php");
  }
		
}
if(isset($_POST['update_data']))
{
  hms_require_role('admin', 'index.php');
	$contact=hms_clean_input($_POST['contact']);
	$status=hms_clean_input($_POST['status']);
  $stmt = mysqli_prepare($con, "update appointmenttb set payment=? where contact=?");
  mysqli_stmt_bind_param($stmt, "ss", $status, $contact);
	$result=mysqli_stmt_execute($stmt);
	if($result)
		header("Location:updated.php");
}




if(isset($_POST['doc_sub']))
{
  hms_require_role('admin', 'index.php');
	$doctor=hms_clean_input($_POST['doctor']);
  $dpassword=hms_clean_input($_POST['dpassword']);
  $demail=hms_clean_input($_POST['demail']);
  $docFees=hms_clean_input($_POST['docFees']);
  $hashedPassword = hms_hash_password($dpassword);
  $stmt = mysqli_prepare($con, "insert into doctb(username,password,email,docFees) values(?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "ssss", $doctor, $hashedPassword, $demail, $docFees);
	$result=mysqli_stmt_execute($stmt);
	if($result)
  {
    hms_audit_log($con, 'doctor.added', 'doctor', $doctor, array('email' => $demail));
		header("Location:admin-dashboard.php#list-settings");
  }
}
?>
