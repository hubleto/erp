<?php

namespace Hubleto\App\Community\About\Controllers;

class About extends \Hubleto\Erp\Controller
{

  public bool $permittedForAllUsers = true;

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'about', 'content' => $this->translate('About') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $tmp = \Composer\InstalledVersions::getInstalledPackages();
    $packages = [];
    foreach ($tmp as $package) {
      $packages[$package] = [
       'version' => \Composer\InstalledVersions::getPrettyVersion($package),
       'reference' => \Composer\InstalledVersions::getReference($package),
      ];
    }

    $this->viewParams['packages'] = $packages;

    $this->setView('@Hubleto:App:Community:About/About.twig');
  }

}
