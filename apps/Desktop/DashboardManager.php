<?php

namespace Hubleto\App\Community\Desktop;

class DashboardManager extends \Hubleto\Framework\Core
{

  /** @var array<int, \Hubleto\App\Community\Desktop\Types\Board> */
  protected array $boards = [];

  public function addBoard(\Hubleto\App\Community\Desktop\Types\Board $board): void
  {
    $this->boards[] = $board;
  }

  public function getBoards(): array
  {
    return $this->boards;
  }

}
