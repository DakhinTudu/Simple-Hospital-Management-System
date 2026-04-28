<?php
function check_login($requiredRole = '')
{
if (!isset($_SESSION['role']) || ($requiredRole !== '' && $_SESSION['role'] !== $requiredRole))
	{
		header("Location: index.php");
    exit();
	}
}
?>