<?php

namespace HubletoMain\Core\Emails;

class EmailWrapper
{

  private $emailProvider;
  private $main;
  private const EMAIL_TEMPLATE = "@hubleto/layouts/Email.twig";

  public function __construct(\HubletoMain $main, EmailProvider $emailProvider) {
    $this->emailProvider = $emailProvider;
    $this->main = $main;
  }

  public function sendResetPasswordEmail(String $login, String $name, String $language, String $token): void
  {
    $greetings = $name == ' ' ? 'Hello from Hubleto!' : 'Dear ' . $name . ',';
    $content = $greetings . '<br><br>
    We received a request to reset your password for your account. If you made this request, please click the button below to set a new password:
    
    <p style="text-align: center;">
      <a href="'. $this->main->config->getAsString('rootUrl') .'/reset-password?token='. $token .'" class="btn--theme">Reset password</a>
    </p>
    
    If you did not request a password reset, please ignore this email. Your password will remain unchanged. <br><br><br>
    
    For security reasons, this link will expire in 15 minutes. <br>
    ';
    $body = $this->main->twig->render(self::EMAIL_TEMPLATE, ['title' => 'Reset your password | Hubleto', 'body' => $content]);

    $this->emailProvider->sendEmail($login, "Reset your password | Hubleto", $body);
  }

  public function sendWelcomeEmail(String $login, String $name, String $language, String $token): void
  {
    $greetings = 'Hello from Hubleto!';
    $content = $greetings . '<br><br>
    Thank you for signing up at our website! We\'re excited to have you on board. Please click the button below to confirm your account and get started.
    <br>
    <p style="text-align: center;">
      <a href="'. $this->main->config->getAsString('rootUrl') .'/reset-password?token='. $token .'" class="btn--theme">Get started</a>
    </p>
    <br>
    
    If you didn\'t sign up for this account, you can safely ignore this email.<br>
    ';
    $body = $this->main->twig->render(self::EMAIL_TEMPLATE, ['title' => 'Reset your password | Hubleto', 'body' => $content]);

    $this->emailProvider->sendEmail($login, "Your Hubleto account has been created!", $body);
  }

}