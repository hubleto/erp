<?php

namespace HubletoMain\Core\Emails;

use ADIOS\Core\Exceptions\GeneralException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailProvider
{

  public \HubletoMain $main;

  private string $defaultEmailTemplate = "@hubleto/layouts/Email.twig";

  private $smtpHost;
  private $smtpPort;
  private $smtpEncryption;
  private $smtpUsername;
  private $smtpPassword;

  public function __construct(\HubletoMain $main, $host, $port, $encryption, $username, $password)
  {
    $this->main = $main;

    $this->smtpHost = $host;
    $this->smtpPort = $port;
    $this->smtpEncryption = $encryption;
    $this->smtpUsername = $username;
    $this->smtpPassword = $password;
  }

  public function getFormattedBody(string $title, string $rawBody, string $template = ''): string
  {
    if (empty($template)) $template = $this->defaultEmailTemplate;
    return $this->main->twig->render($template, ['title' => $title, 'body' => $rawBody]);
  }

  public function send($to, $subject, $rawBody, $template = '', $fromName = 'Hubleto'): bool
  {
    if (!class_exists(PHPMailer::class)) {
      throw new \Exception('PHPMailer is required to send emails. Run `composer require phpmailer/phpmailer` to install it.');
    }

    if (empty($this->smtpHost) || empty($this->smtpUsername) || empty($this->smtpPassword) || empty($this->smtpEncryption) || empty($this->smtpPort)) {
      throw new \Exception('SMTP is not properly configured. Cannot send emails.');
    }

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = $this->smtpHost;
      $mail->SMTPAuth = true;
      $mail->Username = $this->smtpUsername;
      $mail->Password = $this->smtpPassword;
      $mail->SMTPSecure = $this->smtpEncryption;
      $mail->Port = $this->smtpPort;
      $mail->CharSet = "UTF-8";

      $mail->setFrom($this->smtpUsername, $fromName);

      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $this->getFormattedBody($subject, $rawBody, $template);

      $mail->send();
      return true;
    } catch (Exception $e) {
      throw new GeneralException("Mailer Error: " . $mail->ErrorInfo);
    }
  }

  public function sendEmail($to, $subject, $body, $fromName = 'Hubleto')
  {
    if (!class_exists(PHPMailer::class)) {
      throw new \Exception('PHPMailer is required to send emails. Run `composer require phpmailer/phpmailer` to install it.');
    }

    if (empty($this->smtpHost) || empty($this->smtpUsername) || empty($this->smtpPassword) || empty($this->smtpEncryption) || empty($this->smtpPort)) {
      throw new \Exception('SMTP is not properly configured. Cannot send emails.');
    }

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
