<?php

namespace HubletoMain\Core\Emails;

use ADIOS\Core\Exceptions\GeneralException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailProvider
{
  private $smtpHost;
  private $smtpPort;
  private $smtpEncryption;
  private $smtpUsername;
  private $smtpPassword;

  public function __construct($host, $port, $encryption, $username, $password)
  {
    $this->smtpHost = $host;
    $this->smtpPort = $port;
    $this->smtpEncryption = $encryption;
    $this->smtpUsername = $username;
    $this->smtpPassword = $password;
  }

  public function sendEmail($to, $subject, $body, $fromName = 'Hubleto Team')
  {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = $this->smtpHost;
      $mail->SMTPAuth = true;
      $mail->Username = $this->smtpUsername;
      $mail->Password = $this->smtpPassword;
      $mail->SMTPSecure = $this->smtpEncryption;
      $mail->Port = $this->smtpPort;

      $mail->setFrom($this->smtpUsername, $fromName);

      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $body;

      $mail->send();
      return true;
    } catch (Exception $e) {
      throw new GeneralException("Mailer Error: " . $mail->ErrorInfo);
    }
  }
}
