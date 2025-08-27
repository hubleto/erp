<?php

namespace HubletoApp\Community\Desktop;

class DashboardManager extends \Hubleto\Framework\Core
{

  /** @var array<int, \HubletoApp\Community\Desktop\Types\Board> */
  protected array $boards = [];

  public function addBoard(\HubletoApp\Community\Desktop\Types\Board $board): void
  {
    $this->boards[] = $board;
  }

  public function getBoards(): array
  {
    return $this->boards;
  }

}
