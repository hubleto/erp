<?php declare(strict_types=1);

namespace Hubleto\Erp;

class App extends \Hubleto\Framework\App
{
  public function getMcpTools(): array
  {
    return [];
  }

  function secondSidebarTitle(): string
  {
    return '
      <div class="app-main-title"><a href="' . $this->env()->projectUrl . '/' . $this->manifest['rootUrlSlug'] . '">
        <i class="mr-2 ' . $this->manifest['icon'] . '"></i>
        ' . $this->translate($this->manifest['name']) . '
      </a></div>
    ';

  }

  function secondSidebarButton(string $url, string $icon, string $title, int $badge = 0): string
  {
    return '<a
      class="btn ' . ($url == $this->env()->requestedUri ? "btn-primary" : "btn-white") . '"
      href="' . $this->env()->projectUrl . '/' .  $url . '">
        <span class="icon"><i class="' . $icon . '"></i></span>
        <span class="text">' . $this->translate($title) . '</span>
        ' . ($badge > 0 ? '<span class="badge badge-danger ml-auto">' . $badge . '</span>' : '') . '
      </a>
    ';
  }
}
