<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

function sendAppointmentEmail($toEmail, $toName, $date, $time, $dentist, $service) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ap47090@gmail.com'; // gmail account
        $mail->Password   = 'rkiizzniegxcbozc'; // generated app pasword 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('ap47090@gmail.com', 'Auza Dental Clinic');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation - Auza Dental Clinic';
        $mail->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                <h2 style='color:#4a90d9;'>Auza Dental Clinic</h2>
                <p>Hello, <b>$toName</b>!</p>
                <p>Your appointment has been <b style='color:green;'>confirmed</b>.</p>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr>
                        <td style='padding:8px;border:1px solid #ddd;'><b>Date</b></td>
                        <td style='padding:8px;border:1px solid #ddd;'>$date</td>
                    </tr>
                    <tr>
                        <td style='padding:8px;border:1px solid #ddd;'><b>Time</b></td>
                        <td style='padding:8px;border:1px solid #ddd;'>$time</td>
                    </tr>
                    <tr>
                        <td style='padding:8px;border:1px solid #ddd;'><b>Dentist</b></td>
                        <td style='padding:8px;border:1px solid #ddd;'>$dentist</td>
                    </tr>
                    <tr>
                        <td style='padding:8px;border:1px solid #ddd;'><b>Service</b></td>
                        <td style='padding:8px;border:1px solid #ddd;'>$service</td>
                    </tr>
                </table>
                <p style='margin-top:20px;'>Thank you for choosing Auza Dental Clinic!</p>
            </div>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<div style='background:#fee;color:#900;padding:15px;margin:15px;border:2px solid red;'>";
        echo "<b>MAILER ERROR:</b> " . $mail->ErrorInfo;
        echo "</div>";
        return false;
    }
}
?>