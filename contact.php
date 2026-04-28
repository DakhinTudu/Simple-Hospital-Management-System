<?php 
include('include/config.php');
include('include/security.php');
if(isset($_POST['btnSubmit']))
{
	$name = hms_clean_input($_POST['txtName']);
	$email = hms_clean_input($_POST['txtEmail']);
	$contact = hms_clean_input($_POST['txtPhone']);
	$message = hms_clean_input($_POST['txtMsg']);

	$stmt = mysqli_prepare($con, "insert into contact(name,email,contact,message) values(?,?,?,?)");
	mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $contact, $message);
	$result = mysqli_stmt_execute($stmt);
	
	if($result)
    {
    	echo '<script type="text/javascript">'; 
		echo 'alert("Message sent successfully!");'; 
		echo 'window.location.href = "contact.html";';
		echo '</script>';
    }
    else
    {
      echo '<script type="text/javascript">'; 
      echo 'alert("Unable to send your message. Please try again.");'; 
      echo 'window.location.href = "contact.html";';
      echo '</script>';
    }
}
