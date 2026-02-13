<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent;

class CommandPrepareComposer extends \Hubleto\Erp\Cli\Agent\Command
{
  public array $initConfig = [];

  public function parseConfigFile(string $configFile, array $extraConfigsInCommandLine = []): array
  {
    $configStr = (string) file_get_contents($configFile);
    $config = (array) (\Symfony\Component\Yaml\Yaml::parse($configStr) ?? []);

    if (count($extraConfigsInCommandLine)) {
      $extraConfig = (array) (\Symfony\Component\Yaml\Yaml::parse(join('\n', $extraConfigsInCommandLine)) ?? []);
      $config = array_merge($config, $extraConfig);
    }

    return $config;
  }

  public function run(): void
  {

    $configFile = $this->arguments[2] ?? '';
    $composerJsonFile = getcwd() . '/composer.json';

    if (empty($configFile)) {
      $this->terminal()->red("Provide path to .yaml config file.\n");
      exit;
    }

    if (is_file($configFile)) {
      $configStr = (string) file_get_contents($configFile);
      $config = (array) (\Symfony\Component\Yaml\Yaml::parse($configStr) ?? []);
    }

    if (!is_array($config['composer'])) {
      $this->terminal()->red("Config file must contain a part named 'composer'.\n");
      exit;
    }

    if (!is_file($composerJsonFile)) {
      $this->terminal()->red("composer.json file does not exist.\n");
      exit;
    }

    $composer = json_decode(file_get_contents($composerJsonFile), true);
    $composer = array_merge($composer, $config['composer']);
    file_put_contents($composerJsonFile, json_encode($composer, JSON_PRETTY_PRINT));

    $this->terminal()->cyan("\n");
    $this->terminal()->cyan("I have modified 'composer.json' based on your config.\n");
    $this->terminal()->cyan("Run `composer update`.\n");
    $this->terminal()->cyan("\n");
  }
}
