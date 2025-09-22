<?php declare(strict_types=1);

namespace Hubleto\Erp\Emails;

use Hubleto\Framework\Exceptions\GeneralException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailProvider extends \Hubleto\Framework\Core
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
    $this->smtpHost = $this->config()->getAsString('smtpHost', '');
    $this->smtpPort = $this->config()->getAsInteger('smtpPort', 0);
    $this->smtpEncryption = $this->config()->getAsString('smtpEncryption', 'ssl');
    $this->smtpUsername = $this->config()->getAsString('smtpLogin', '');
    $this->smtpPassword = $this->config()->getAsString('smtpPassword', '');
  }

  public function getFormattedBody(string $title, string $rawBody, string $template = ''): string
  {
    if (empty($template)) {
      $template = $this->defaultEmailTemplate;
    }
    return $this->renderer()->renderView($template, [
      'title' => $title,
      'body' => $rawBody,
      'env' => $this->env(),
    ]);
  }

  /**
   * [Description for send]
   *
   * @param string $to
   * @param string $subject
   * @param string $rawBody
   * @param string $template
   * @param string $fromName
   * 
   * @return bool
   * 
   */
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

  /**
   * [Description for sendEmail]
   *
   * @param string $to
   * @param string $subject
   * @param string $body
   * @param string $fromName
   * 
   * @return bool
   * 
   */
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

  /**
   * [Description for sendResetPasswordEmail]
   *
   * @param String $login
   * @param String $name
   * @param String $language
   * @param String $token
   * 
   * @return void
   * 
   */
  public function sendResetPasswordEmail(String $login, String $name, String $language, String $token): void
  {
    $greetings = $name == ' ' ? 'Hello from Hubleto!' : 'Dear ' . $name . ',';
    $body = $greetings . '<br><br>
    We received a request to reset your password for your account. If you made this request, please click the button below to set a new password:
    
    <p style="text-align: center;">
      <a href="'. $this->env()->projectUrl .'/reset-password?token='. $token .'" class="btn--theme">Reset password</a>
    </p>
    
    If you did not request a password reset, please ignore this email. Your password will remain unchanged. <br><br><br>
    
    For security reasons, this link will expire in 15 minutes. <br>
    ';

    $this->send($login, "Reset your password | Hubleto", $body);
  }

  /**
   * [Description for sendWelcomeEmail]
   *
   * @param String $login
   * @param String $name
   * @param String $language
   * @param String $token
   * 
   * @return void
   * 
   */
  public function sendWelcomeEmail(String $login, String $name, String $language, String $token): void
  {
    $greetings = 'Hello from Hubleto!';
    $body = $greetings . '<br><br>
    Thank you for signing up at our website! We\'re excited to have you on board. Please click the button below to confirm your account and get started.
    <br>
    <p style="text-align: center;">
      <a href="'. $this->env()->projectUrl .'/reset-password?token='. $token .'" class="btn--theme">Get started</a>
    </p>
    <br>
    
    If you didn\'t sign up for this account, you can safely ignore this email.<br>
    ';

    $this->sendEmail($login, "Your Hubleto account has been created!", $body);
  }

}
