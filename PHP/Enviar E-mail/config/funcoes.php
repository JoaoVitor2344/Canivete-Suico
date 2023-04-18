<?php 

function sendMail($nomeDestino, $emailDestino, $assunto, $msg, PDO $conn, $arquivo = "") {
  $mail = new PHPMailer();
  $mail->isSMTP();
  $mail->SMTPDebug   = 1;
  $mail->SMTPSecure = 'ssl';
  $mail->Debugoutput = 'html';
  $mail->Host        = getStatic($conn, 'site_mail_host');
  $mail->Port        = getStatic($conn, 'site_mail_porta');
  $mail->SMTPAuth    = true;
  $mail->Username    = getStatic($conn, 'site_mail_user'); 
  $mail->Password    = getStatic($conn, 'site_mail_pass');
  $mail->SetFrom(getStatic($conn, 'site_mail_user'), getStatic($conn, 'site_mail_exibicao'));
  $mail->AddAddress($emailDestino, utf8_decode($nomeDestino));
  $mail->Subject = utf8_decode($assunto);
  $mail->msgHTML(utf8_decode($msg));
  if($arquivo == true)
    $mail->AddAttachment($arquivo);

  if(!$mail->send()){
    saveLog($conn, "Falha envio de e-mail: ".$mail->ErrorInfo);
    return false;
  }else{
    return true;
  }
}

?>
