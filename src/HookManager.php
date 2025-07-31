<?php declare(strict_types=1);

namespace HubletoMain;

class HookManager
{
  /** @var array<\HubletoMain\Hook> */
  protected array $hooks = [];

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function init(): void
  {
    $hooks = @\Hubleto\Framework\Helper::scanDirRecursively($this->main->srcFolder . '/hooks');
    foreach ($hooks as $hook) {
      if (!\str_ends_with($hook, '.php')) continue;
      $hookClass = '\\HubletoMain\\Hook\\' . str_replace('/', '\\', $hook);
      $hookClass = str_replace('.php', '', $hookClass);
      $this->addHook($hookClass);
    }

    $hooks = @\Hubleto\Framework\Helper::scanDirRecursively($this->main->projectFolder . '/src/hooks');
    foreach ($hooks as $hook) {
      if (!\str_ends_with($hook, '.php')) continue;
      $hookClass = '\\HubletoProject\\Hook\\' . str_replace('/', '\\', $hook);
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
    if (is_subclass_of($hookClass, \HubletoMain\Hook::class)) {
      $this->hooks[$hookClass] = new $hookClass($this->main);
    }
  }

  public function getHooks(): array
  {
    return $this->hooks;
  }

  public function run(string $trigger, array $args): void
  {
    foreach ($this->hooks as $hookClass => $hook) {
      $hook->run($trigger, $args);
    }
  }

}
