<?php

namespace HubletoApp\Community\Desktop\Types;

class Board {

  public string $title = '';
  public string $rendererUrlSlug = '';

  public function __construct(string $title, string $rendererUrlSlug)
  {
    $this->title = $title;
    $this->rendererUrlSlug = $rendererUrlSlug;
  }

}