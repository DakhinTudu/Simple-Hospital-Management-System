<?php
include('include/config.php');
include('include/security.php');
hms_require_role('admin', 'index.php');

$filename = "doctors_list_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, array('Doctor Name', 'Specialization', 'Email', 'Fees'));

$query = "SELECT username, spec, email, docFees FROM doctb ORDER BY username ASC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}
fclose($output);
exit();
?>
