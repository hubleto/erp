<?php declare(strict_types=1);

namespace HubletoMain\Emails;

use Hubleto\Framework\Exceptions\GeneralException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailProvider
{

  private string $defaultEmailTemplate = "@hubleto-main/layouts/Email.twig";

  private string $smtpHost;
  private int $smtpPort;
  private string $smtpEncryption;
  private string $smtpUsername;
  private string $smtpPassword;

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function init(): void
  {
    $this->smtpHost = $this->main->getConfig()->getAsString('smtpHost', '');
    $this->smtpPort = $this->main->getConfig()->getAsInteger('smtpPort', 0);
    $this->smtpEncryption = $this->main->getConfig()->getAsString('smtpEncryption', 'ssl');
    $this->smtpUsername = $this->main->getConfig()->getAsString('smtpLogin', '');
    $this->smtpPassword = $this->main->getConfig()->getAsString('smtpPassword', '');
  }

  public function getFormattedBody(string $title, string $rawBody, string $template = ''): string
  {
    if (empty($template)) {
      $template = $this->defaultEmailTemplate;
    }
    return $this->main->twig->render($template, ['title' => $title, 'body' => $rawBody]);
  }

  public function send(string $to, string $subject, string $rawBody, string $template = '', string $fromName = 'Hubleto'): bool
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

  public function sendEmail(string $to, string $subject, string $body, string $fromName = 'Hubleto'): bool
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
