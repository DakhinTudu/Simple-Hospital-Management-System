<?php
include('include/config.php');
include('include/security.php');

if($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

if(!isset($_GET['ID'])) {
    die("Invalid request");
}

$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
$billId = (int)$_GET['ID'];

require_once("TCPDF/tcpdf.php");

function get_prescription_html($con, $billId) {
    $output = '';
    $stmt = mysqli_prepare($con, "SELECT p.pid, p.ID, p.fname, p.lname, p.doctor, p.appdate, p.apptime, p.disease, p.allergy, p.prescription, a.docFees 
                                  FROM prestb p 
                                  INNER JOIN appointmenttb a ON p.ID=a.ID 
                                  WHERE p.ID=?");
    mysqli_stmt_bind_param($stmt, "i", $billId);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_array($query)) {
        $output .= '
        <div style="font-family: Arial, sans-serif; background-color: #ffffff; margin: 0; padding: 0; color: #1e293b;">
            <div style="padding: 30px 50px; background-color: #ffffff;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                    <h2 style="color: #102742; margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 2px;">Medical Prescription</h2>
                    <p style="font-size: 10px; color: #64748b; margin-top: 5px;">ID: #'.$row["ID"].' | Issued: '.date('d M Y').'</p>
                </div>

                <table style="width: 100%; margin-bottom: 25px; background-color: #f8fafc; padding: 15px; border: 1px solid #f1f5f9; border-radius: 5px;">
                    <tr>
                        <td style="width: 50%;">
                            <span style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">Patient Information</span><br>
                            <strong style="font-size: 15px; color: #102742;">'.$row["fname"].' '.$row["lname"].'</strong><br>
                            <span style="font-size: 10px; color: #475569;">PID: '.$row["pid"].'</span>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <span style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">Attending Physician</span><br>
                            <strong style="font-size: 15px; color: #102742;">Dr. '.$row["doctor"].'</strong><br>
                            <span style="font-size: 10px; color: #475569;">Global Hospitals BHUBANESWAR</span>
                        </td>
                    </tr>
                </table>

                <div style="margin-bottom: 20px;">
                    <h4 style="color: #102742; border-left: 4px solid #102742; padding-left: 10px; margin-bottom: 8px; font-size: 13px; text-transform: uppercase;">Diagnosis</h4>
                    <p style="font-size: 12px; color: #b91c1c; font-weight: bold; padding-left: 14px;">'.$row["disease"].'</p>
                </div>

                <div style="margin-bottom: 40px; min-height: 280px; border: 1px solid #f1f5f9; padding: 15px; border-radius: 5px; background-color: #ffffff;">
                    <h4 style="color: #102742; margin-bottom: 12px; font-size: 13px; text-transform: uppercase;">Rx / Prescribed Treatment</h4>
                    <div style="font-size: 11px; line-height: 1.8; color: #1e293b;">
                        '.nl2br($row["prescription"]).'
                    </div>
                </div>

                <table style="width: 100%; margin-top: 20px; background-color: #ffffff;">
                    <tr>
                        <td style="width: 70%; font-size: 9px; color: #94a3b8; vertical-align: bottom; line-height: 1.4;">
                            * This is an official digital record of Global Hospitals.<br>
                            * Please present this document for all follow-up visits.<br>
                            * Validity: 30 Days from date of issue.
                        </td>
                        <td style="width: 30%; text-align: center;">
                            <div style="border-bottom: 1px solid #102742; height: 50px; margin-bottom: 5px;"></div>
                            <strong style="font-size: 10px; color: #102742; text-transform: uppercase;">Authorized Signature</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>';
    } else {
        $output = "<h1>Record not found</h1>";
    }
    return $output;
}

$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$obj_pdf->SetTitle("Prescription_" . $billId);
$obj_pdf->SetMargins(5, 0, 5); // 5mm side margins
$obj_pdf->SetPrintHeader(false);
$obj_pdf->SetPrintFooter(false);
$obj_pdf->SetAutoPageBreak(TRUE, 5);
$obj_pdf->SetFont('helvetica', '', 11);
$obj_pdf->AddPage();

// 1. Header with equal 5mm side margins (A4 width 210 - 10 = 200)
$header_html = '
<table style="width: 100%; background-color: #102742; color: #ffffff; padding: 20px 40px;">
    <tr>
        <td style="width: 60%; vertical-align: middle;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: bold;">GLOBAL HOSPITALS</h1>
            <p style="font-size: 10px; margin: 2px 0; opacity: 0.8; text-transform: uppercase;">Clinical Excellence</p>
        </td>
        <td style="width: 40%; text-align: right; vertical-align: middle; font-size: 9px; line-height: 1.5;">
            <strong style="color: #38bdf8;">Bhubaneswar Branch</strong><br>
            Plot No. 12, Patia, Odisha 751024<br>
            Ph: +91 674 2725 123
        </td>
    </tr>
</table>';
$obj_pdf->writeHTMLCell(200, 0, 5, 0, $header_html, 0, 1, true, true, 'L', true);

// 2. Main Content (Starts at Y=40, width 200)
$content = get_prescription_html($con, $billId);
$obj_pdf->SetY(40);
$obj_pdf->writeHTMLCell(200, 0, 5, 40, $content, 0, 1, true, true, 'L', true);

// 3. Footer with equal 5mm side margins
$footer_html = '
<table style="width: 100%; background-color: #102742; color: #ffffff; padding: 20px 40px;">
    <tr>
        <td style="text-align: center; font-size: 9px; letter-spacing: 1px;">
            GLOBAL HOSPITALS BHUBANESWAR • PATIA, Odisha 751024 • WWW.GLOBALHOSPITALS.IN
        </td>
    </tr>
</table>';
$obj_pdf->writeHTMLCell(200, 0, 5, 277, $footer_html, 0, 1, true, true, 'L', true);

ob_end_clean();
$obj_pdf->Output("Prescription_" . $billId . ".pdf", 'I');
exit();
?>
