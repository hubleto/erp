<?php

namespace HubletoApp\Community\Desktop\Types;

class Board
{
  public string $title = '';
  public string $boardUrlSlug = '';

  public function __construct(string $title, string $boardUrlSlug)
  {
    $this->title = $title;
    $this->boardUrlSlug = $boardUrlSlug;
  }

}
