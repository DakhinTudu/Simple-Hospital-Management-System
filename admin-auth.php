<?php
session_start();
include('include/config.php');
include('include/security.php');
if(isset($_POST['adsub'])){
	$username=hms_clean_input($_POST['username1']);
	$password=hms_clean_input($_POST['password2']);
  $stmt = mysqli_prepare($con, "select username,password from admintb where username=? limit 1");
  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
	if($result && mysqli_num_rows($result)===1)
	{
    $row = mysqli_fetch_assoc($result);
    if (hms_verify_password($password, $row['password'])) {
      if (!hms_is_password_hashed($row['password'])) {
        $newHash = hms_hash_password($password);
        $updateStmt = mysqli_prepare($con, "update admintb set password=? where username=?");
        mysqli_stmt_bind_param($updateStmt, "ss", $newHash, $username);
        mysqli_stmt_execute($updateStmt);
      }
  		hms_login_user('admin', array('username' => $username));
  		header("Location: admin-dashboard.php");
      exit();
    }
	}
	else
		// header("Location:error2.php");
		echo("<script>alert('Invalid Username or Password. Try Again!');
          window.location.href = 'index.php';</script>");
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




function display_docs()
{
	global $con;
	$query="select * from doctb";
	$result=mysqli_query($con,$query);
	while($row=mysqli_fetch_array($result))
	{
		$name=$row['name'];
		# echo'<option value="" disabled selected>Select Doctor</option>';
		echo '<option value="'.$name.'">'.$name.'</option>';
	}
}

if(isset($_POST['doc_sub']))
{
  hms_require_role('admin', 'index.php');
	$name=hms_clean_input($_POST['name']);
  $stmt = mysqli_prepare($con, "insert into doctb(name) values(?)");
  mysqli_stmt_bind_param($stmt, "s", $name);
	$result=mysqli_stmt_execute($stmt);
	if($result)
		header("Location:adddoc.php");
}
