<?php

namespace HubletoApp\Community\About\Controllers;

class About extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'about', 'content' => $this->translate('About') ],
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

    $this->setView('@HubletoApp:Community:About/About.twig');
  }

}
