<?php declare(strict_types=1);

namespace Hubleto\Erp\Interfaces;

interface EmailProviderInterface
{

  public function getFormattedBody(string $title, string $rawBody, string $template = ''): string;
  public function send(string $to, string $subject, string $rawBody, string $template = '', string $fromName = 'Hubleto'): bool;
  public function sendEmail(string $to, string $subject, string $body, string $fromName = 'Hubleto'): bool;
  public function sendResetPasswordEmail(String $login, String $name, String $language, String $token): void;
  public function sendWelcomeEmail(String $login, String $name, String $language, String $token): void;

}
