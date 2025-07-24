<?php

namespace HubletoApp\Community\Tools;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;

  /** @var array<int, array<\Hubleto\Framework\App, array>> */
  private array $tools = [];

  public function init(): void
  {
    parent::init();
    $this->main->router->httpGet([
      '/^tools\/?$/' => Controllers\Dashboard::class,
    ]);
  }

  public function addTool(\Hubleto\Framework\App $app, array $tool): void
  {
    $this->tools[] = [$app, $tool];
  }

  public function getTools(): array
  {
    $tools = [];
    foreach ($this->tools as $tool) {
      $tools[] = $tool[1];
    }

    $titles = array_column($tools, 'title');
    array_multisort($titles, SORT_ASC, $tools);
    return $tools;
  }
}
