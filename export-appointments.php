<?php
include('include/config.php');
include('include/security.php');
hms_require_role('admin', 'index.php');

$filename = "appointments_report_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, array('ID', 'Patient First Name', 'Patient Last Name', 'Doctor', 'Fees', 'Date', 'Time', 'User Status', 'Doctor Status'));

$query = "SELECT ID, fname, lname, doctor, docFees, appdate, apptime, userStatus, doctorStatus FROM appointmenttb ORDER BY appdate DESC, apptime DESC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $row['userStatus'] = ($row['userStatus'] == 1) ? 'Active' : 'Cancelled';
    $row['doctorStatus'] = ($row['doctorStatus'] == 1) ? 'Active' : 'Cancelled';
    fputcsv($output, $row);
}
fclose($output);
exit();
?>
