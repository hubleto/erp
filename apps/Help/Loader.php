<?php

namespace HubletoApp\Community\Help;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  /** @var array<string, string> */
  public array $hotTips = [];

  /** @var array<string, array<string, string>> */
  public array $contextHelpUrls = [];

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^help\/?$/' => Controllers\Help::class,
    ]);
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Help/Controllers/Help",
  //     "HubletoApp/Community/Help/Help",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

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
