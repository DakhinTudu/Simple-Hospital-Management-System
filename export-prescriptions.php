<?php
include('include/config.php');
include('include/security.php');
// Allowed for admin and doctor
if($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

$filename = "prescriptions_log_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, array('Patient', 'Doctor', 'Appointment ID', 'Date', 'Diagnosis', 'Prescription'));

$query = "SELECT fname, lname, doctor, ID, appdate, disease, prescription FROM prestb ORDER BY appdate DESC";
if($_SESSION['role'] === 'doctor') {
    $doctor = $_SESSION['dname'];
    $query = "SELECT fname, lname, doctor, ID, appdate, disease, prescription FROM prestb WHERE doctor='$doctor' ORDER BY appdate DESC";
}

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $fullName = $row['fname'] . ' ' . $row['lname'];
    fputcsv($output, array(
        $fullName,
        $row['doctor'],
        '#' . $row['ID'],
        $row['appdate'],
        $row['disease'],
        $row['prescription']
    ));
}
fclose($output);
exit();
?>
