<?php

namespace HubletoApp\Community\Desktop;

class DashboardManager {

  public \HubletoMain $main;

  /** @var array<int, \HubletoApp\Community\Desktop\Types\Board> */
  protected array $boards = [];

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function addBoard(\HubletoApp\Community\Desktop\Types\Board $board): void
  {
    $this->boards[] = $board;
  }

  public function getBoards(): array
  {
    return $this->boards;
  }

}