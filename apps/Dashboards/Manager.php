<?php

namespace Hubleto\App\Community\Dashboards;

class Manager extends \Hubleto\Framework\Core
{

  protected array $boards = [];

  public function getBoards(): array
  {
    return $this->boards;
  }

  public function addBoard(\Hubleto\Framework\Interfaces\AppInterface $app, string $title, string $boardUrlSlug): void
  {
    $this->boards[$boardUrlSlug] = [
      'app' => $app,
      'title' => $title,
      'boardUrlSlug' => $boardUrlSlug,
    ];
  }

}