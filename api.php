<?php
header("Content-Type: application/json");

// ============================
// AUTO DETECT SERVER
// ============================

// detect hostname server
$server_domain = $_SERVER['SERVER_NAME'] ?? gethostname();

// detect sendmail path
$sendmail_path = ini_get("sendmail_path");
if(!$sendmail_path){
    $sendmail_path = "/usr/sbin/sendmail";
}

// detect default email
$from_email = "no-reply@" . $server_domain;


// ============================
// INPUT
// ============================

$to      = $_POST['to'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';
$method  = $_POST['method'] ?? 'auto';

if(!$to){
    echo json_encode(["status"=>"error","msg"=>"email target kosong"]);
    exit;
}

// ============================
// HEADER
// ============================

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: ".$from_email."\r\n";


// ============================
// METHOD AUTO
// ============================

if($method == "auto"){

    // coba mail()
    if(function_exists("mail")){
        if(mail($to,$subject,$message,$headers)){
            echo json_encode(["status"=>"success","method"=>"php_mail"]);
            exit;
        }
    }

    // fallback sendmail
    if(file_exists("/usr/sbin/sendmail")){
        $body = "To: $to\n";
        $body .= "Subject: $subject\n";
        $body .= $headers."\n";
        $body .= $message;

        $process = popen("/usr/sbin/sendmail -t","w");
        fputs($process,$body);
        pclose($process);

        echo json_encode(["status"=>"success","method"=>"sendmail"]);
        exit;
    }

    echo json_encode(["status"=>"failed","msg"=>"no mail method"]);
}


// ============================
// METHOD PHP MAIL
// ============================

elseif($method == "mail"){

    if(mail($to,$subject,$message,$headers)){
        echo json_encode(["status"=>"success","method"=>"mail"]);
    }else{
        echo json_encode(["status"=>"failed"]);
    }

}


// ============================
// METHOD SENDMAIL
// ============================

elseif($method == "sendmail"){

    $body = "To: $to\n";
    $body .= "Subject: $subject\n";
    $body .= $headers."\n";
    $body .= $message;

    $process = popen("/usr/sbin/sendmail -t","w");
    fputs($process,$body);
    pclose($process);

    echo json_encode(["status"=>"success","method"=>"sendmail"]);

}

?>
