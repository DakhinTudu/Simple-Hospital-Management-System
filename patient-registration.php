<?php
session_start();
include('include/config.php');
include('include/security.php');
if(isset($_POST['patsub1'])){
	$fname=hms_clean_input($_POST['fname']);
  $lname=hms_clean_input($_POST['lname']);
  $gender=hms_clean_input($_POST['gender']);
  $email=hms_clean_input($_POST['email']);
  $contact=hms_clean_input($_POST['contact']);
	$password=hms_clean_input($_POST['password']);
  $cpassword=hms_clean_input($_POST['cpassword']);
  if($password==$cpassword){
    if (!hms_is_valid_email($email)) {
      header("Location:error.php");
      exit();
    }
    $hashedPassword = hms_hash_password($password);
    $stmt = mysqli_prepare($con, "insert into patreg(fname,lname,gender,email,contact,password,cpassword) values (?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssssss", $fname, $lname, $gender, $email, $contact, $hashedPassword, $hashedPassword);
    $result=mysqli_stmt_execute($stmt);
    if($result){
        hms_login_user('patient', array(
          'pid' => (int)mysqli_insert_id($con),
          'username' => $fname." ".$lname,
          'fname' => $fname,
          'lname' => $lname,
          'gender' => $gender,
          'contact' => $contact,
          'email' => $email
        ));
        header("Location: patient-dashboard.php");
        exit();
    } 
  }
  else{
    header("Location:error.php");
    exit();
  }
}
if(isset($_POST['update_data']))
{
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
	$name=hms_clean_input($_POST['name']);
  $stmt = mysqli_prepare($con, "insert into doctb(name) values(?)");
  mysqli_stmt_bind_param($stmt, "s", $name);
	$result=mysqli_stmt_execute($stmt);
	if($result)
		header("Location: admin-dashboard.php#list-settings");
}
?>
