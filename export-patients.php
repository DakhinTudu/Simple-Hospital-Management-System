<?php
include('include/config.php');
include('include/security.php');
hms_require_role('admin', 'index.php');

$filename = "patients_list_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, array('First Name', 'Last Name', 'Gender', 'Email', 'Contact'));

$query = "SELECT fname, lname, gender, email, contact FROM patreg ORDER BY fname ASC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}
fclose($output);
exit();
?>
