<?php

namespace HubletoMain\Core;

class HookManager
{

  public \HubletoMain $main;

  /** @var array<\HubletoMain\Core\Controller\HookController> */
  protected array $hooks = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function init(): void
  {
    $hooks = @\ADIOS\Core\Helper::scanDirRecursively($this->main->config->getAsString('srcFolder') . '/hooks');
    foreach ($hooks as $hook) {
      $hookClass = '\\HubletoMain\\Hook\\' . str_replace('/', '\\', $hook);
      $hookClass = str_replace('.php', '', $hookClass);
      $this->addHook($hookClass);
    }

    $hooks = @\ADIOS\Core\Helper::scanDirRecursively($this->main->config->getAsString('rootFolder') . '/hooks');
    foreach ($hooks as $hook) {
      $hookClass = '\\HubletoCustom\\Hook\\' . str_replace('/', '\\', $hook);
      $hookClass = str_replace('.php', '', $hookClass);
      $this->addHook($hookClass);
    }
  }

  public function log(string $msg): void
  {
    $this->main->logger->info($msg);
  }

  public function addHook(string $hookClass): void
  {
    $this->hooks[$hookClass] = new $hookClass($this->main);
  }

  public function getHooks(): array
  {
    return $this->hooks;
  }

  public function run(string $trigger, array $args) {
    foreach ($this->hooks as $hookClass => $hook) {
      $hook->run($trigger, $args);
    }
  }

}