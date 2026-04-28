<?php
include('include/config.php');
include('include/security.php');

header('Content-Type: application/json');

$doctor = isset($_GET['doctor']) ? hms_clean_input($_GET['doctor']) : '';
$appdate = isset($_GET['appdate']) ? hms_clean_input($_GET['appdate']) : '';

$defaultSlots = array(
    '08:00:00' => '8:00 AM',
    '10:00:00' => '10:00 AM',
    '12:00:00' => '12:00 PM',
    '14:00:00' => '2:00 PM',
    '16:00:00' => '4:00 PM'
);

if ($doctor === '' || $appdate === '') {
    echo json_encode(array('slots' => array()));
    exit();
}

$available = array();
foreach ($defaultSlots as $timeValue => $label) {
    if (hms_is_slot_free($con, $doctor, $appdate, $timeValue)) {
        $available[] = array('value' => $timeValue, 'label' => $label);
    }
}

echo json_encode(array('slots' => $available));
?>
