<?php

namespace HubletoMain;

class CoreClass
{
  public Loader $main;

  public function __construct(Loader $main)
  {
    $this->main = $main;
  }
}