<?php

namespace Hubleto\Erp;

/**
 * Methods to support locale in Hubleto project.
 */
class Locale extends \Hubleto\Framework\Locale
{

  /**
   * [Description for getTimezone]
   *
   * @return string
   * 
   */
  public function getTimezone(): string
  {
    $defaultTimezone = $this->config()->getAsString('locale/timezone', 'Europe/London');
    $userTimezone = $this->authProvider()->getUser()['timezone'];

    return (empty($userTimezone) ? $defaultTimezone : $userTimezone);
  }

  public function getAvailableLanguages(): array
  {
    return $this->config()->getAsArray('availableLanguages', [
      "en" => [ "flagImage" => "en.jpg", "name" => "English" ],
      "de" => [ "flagImage" => "de.jpg", "name" => "Deutsch" ],
      "es" => [ "flagImage" => "es.jpg", "name" => "Español" ],
      "fr" => [ "flagImage" => "fr.jpg", "name" => "Francais" ],
      "it" => [ "flagImage" => "it.jpg", "name" => "Italiano" ],
      "pl" => [ "flagImage" => "pl.jpg", "name" => "Polski" ],
      "ro" => [ "flagImage" => "ro.jpg", "name" => "Română" ],
      "cs" => [ "flagImage" => "cs.jpg", "name" => "Česky" ],
      "sk" => [ "flagImage" => "sk.jpg", "name" => "Slovensky" ],
    ]);
  }

}