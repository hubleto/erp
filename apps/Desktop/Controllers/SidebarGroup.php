<?php declare(strict_types=1);

namespace Hubleto\App\Community\Desktop\Controllers;

use Hubleto\App\Community\Desktop\Loader;
class SidebarGroup extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = true;
  public bool $permittedForAllUsers = true;
  public bool $hideDefaultDesktop = false;
  public string $translationContext = 'Hubleto\\Erp\\Loader::Controllers\\SidebarGroup';

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Loader */
    $desktopApp = $this->getService(Loader::class);

    $sidebarGroups = $desktopApp->getSidebarGroups();
    $sidebarGroup = $sidebarGroups[$this->viewParams['group']] ?? [];
    $this->viewParams['sidebarGroup'] = $sidebarGroup;

    $apps = [];

    $enabledApps = $this->appManager()->getEnabledApps();
    foreach ($enabledApps as $app) {
      if (($app->manifest['sidebarGroup'] ?? "") === $this->viewParams['group']) {
        $apps[] = [
          'name' => $app->manifest['name'] ?? $app->namespace,
          'icon' => $app->manifest['icon'] ?? 'fa fa-puzzle-piece',
          'rootUrlSlug' => $app->manifest['rootUrlSlug'] ?? '',
          'namespace' => $app->namespace,
        ];
      }
    }

    // Boards temporarily disabled, some of the boards caused errors.
    // $boardManager = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    // $boards = [];
    // $iterCount = 0;
    // foreach ($boardManager->getBoards() as $board) {
    //   if (($board['app']->manifest['sidebarGroup'] ?? "") === $this->viewParams['group']) {
    //     $boards[] = [
    //       "id" => ++$iterCount,
    //       "id_dashboard" => -1,
    //       "board_url_slug" => $board['boardUrlSlug'],
    //       "title" => $board['title'],
    //       "configuration" => ""
    //     ];
    //   }
    // }

    $this->viewParams['apps'] = $apps;
    // $this->viewParams['boards'] = $boards;

    $this->setView('@Hubleto:App:Community:Desktop/SidebarGroup.twig');
  }

}
