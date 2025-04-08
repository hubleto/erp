<?php

namespace HubletoMain\Core;

class Help {
  public \HubletoMain $main;

  /** @var array<string, string> */
  public array $hotTips = [];

  /** @var array<string, array<string, string>> */
  public array $contextHelpUrls = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function addHotTip(string $slugRegExp, string $title): void
  {
    $this->hotTips[$slugRegExp] = $title;
  }

  public function addContextHelpUrls(string $slugRegExp, array $urls): void
  {
    $this->contextHelpUrls[$slugRegExp] = $urls;
  }

  public function getCurrentContextHelpUrls(string $slugRegExp): array
  {
    foreach ($this->contextHelpUrls as $regExp => $urls) {
      if (preg_match($regExp, $slugRegExp)) {
        return $urls;
      }
    }

    return [];
  }

}
