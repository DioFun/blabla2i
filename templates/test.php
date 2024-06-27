<?php
// for use PHP Mailer without composer :

// ex. Create a folder in root/PHPMAILER
// Put on this folder this 3 files find in "src"
// folder of the distribution :
// PHPMailer.php , SMTP.php , Exception.php

// include PHP Mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include dirname(__DIR__) .'/PHPMailer/src/PHPMailer.php';
include dirname(__DIR__) .'/PHPMailer/src/SMTP.php';
include dirname(__DIR__) .'/PHPMailer/src/Exception.php';
/*
*
* Function send_mail_by_PHPMailer($to, $from, $subject, $message);
* send a mail by PHPMailer method
* @Param $to -> mail to send
* @Param $from -> sender of mail
* @Param $subject -> suject of mail
* @Param $message -> html content with datas
* @Return true if success / Json encoded error message if error
* !! need -> classes/Exception.php - classes/PHPMailer.php - classes/SMTP.php
*
*/
function send_mail_by_PHPMailer($to, $from, $subject, $message){
$mail = new PHPMailer(true);

try {
// Server settings
$mail->SMTPDebug = 2;                                       // Enable verbose debug output
$mail->isSMTP();                                            // Set mailer to use SMTP
$mail->Host       = 'mail.rezoleo.fr';                     // Specify main and backup SMTP servers
$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
$mail->Username   = 'blabla2i@rezoleo.fr';
$mail->SMTPSecure = 'tls';// SMTP username
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->Password   = 'blabla2i';                               // Enable TLS encryption, `ssl` also accepted
$mail->Port       = 587;                                    // TCP port to connect to

// Recipients
$mail->setFrom('blabla2i@rezoleo.fr', 'Mailer');
$mail->addAddress('baptiste.drouet@centrale.centralelille.fr', 'Recipient Name');     // Add a recipient
$mail->addReplyTo('blabla2i@rezoleo.fr', 'Information');

// Content
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

$mail->send();
echo 'Message has been sent';
} catch (Exception $e) {
echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
//    // SEND MAIL by PHP MAILER
//    $mail = new PHPMailer();
//    $mail->CharSet = 'UTF-8';
//    $mail->isSMTP(); // Use SMTP protocol
//    $mail->Host = 'mail.rezoleo.fr'; // Specify  SMTP server
//    $mail->SMTPAuth = true; // Auth. SMTP
//    $mail->Username = 'blabla2i@rezoleo.fr'; // Mail who send by PHPMailer
//    $mail->Password = 'blabla2i'; // your pass mail box
//    $mail->SMTPSecure = PHPMailer::; // Accept SSL
//    $mail->Port = 465; // port of your out server
//    $mail->setFrom($from); // Mail to send at
//    $mail->addAddress($to); // Add sender
//    $mail->addReplyTo($from); // Adress to reply
//    $mail->isHTML(true); // use HTML message
//    $mail->Subject = $subject;
//    $mail->Body = $message;
//
//    // SEND
//    if( !$mail->send() ){
//
//        // render error if it is
//        $tab = array('error' => 'Mailer Error: '.$mail->ErrorInfo );
//        echo json_encode($tab);
//        exit;
//    }
//    else{
//        // return true if message is send
//        return true;
//    }

}
dd(send_mail_by_PHPMailer("baptiste.drouet@centrale.centralelille.fr", "blabla2i@rezoleo.fr", "test", "youpi !"));