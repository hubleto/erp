<?php declare(strict_types=1);

namespace HubletoMain;

class HookManager extends \Hubleto\Framework\CoreClass
{
  /** @var array<\HubletoMain\Hook> */
  protected array $hooks = [];

  public function init(): void
  {
    $hooks = @\Hubleto\Framework\Helper::scanDirRecursively($this->main->srcFolder . '/../hooks');
    foreach ($hooks as $hook) {
      if (!\str_ends_with($hook, '.php')) continue;
      $hookClass = '\\HubletoMain\\Hook\\' . str_replace('/', '\\', $hook);
      $hookClass = str_replace('.php', '', $hookClass);
      $this->addHook($hookClass);
    }

    foreach ($this->getAppManager()->getInstalledApps() as $appNamespace => $app) {
      $hooks = @\Hubleto\Framework\Helper::scanDirRecursively($app->srcFolder . '/Hooks');
      // var_dump($appNamespace);var_dump($hooks);
      foreach ($hooks as $hook) {
        if (!\str_ends_with($hook, '.php')) continue;
        $hookClass = '\\' . $appNamespace . '\\Hooks\\' . str_replace('/', '\\', $hook);
        $hookClass = str_replace('.php', '', $hookClass);
        $this->addHook($hookClass);
      }
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
    // } else {
    //   $tmp = new $hookClass($this->main);
    //   var_dump(is_subclass_of($hookClass, \HubletoMain\Hook::class));
    //   var_dump($hookClass);
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
