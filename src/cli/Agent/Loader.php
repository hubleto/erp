<?php

namespace HubletoMain\Cli\Agent;

class Loader {

  public \HubletoMain $main;

  /** @var resource|false */
  public $clih;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    $this->clih = fopen("php://stdin", "r");
  }

  public function isLaunchedFromTerminal(): bool
  {
    return (php_sapi_name() === 'cli');
  }

  public function color(string $colorName): void
  {
    if (php_sapi_name() !== 'cli') return;

    $sequences = [
      'red' => "\033[31m",
      'green' => "\033[32m",
      'yellow' => "\033[33m",
      'blue' => "\033[34m",
      'cyan' => "\033[36m",
      'white' => "\033[37m",
      'bg-default' => "\033[49m",
      'bg-cyan' => "\033[46m",
    ];

    if (isset($sequences[$colorName])) {
      echo $sequences[$colorName];
    }
  }

  public function readRaw(): string
  {
    $input = ($this->clih ? (string) fgets($this->clih) : '');
    $input = trim($input);
    return $input;
  }

  public function read(string $message, string $default = ''): string
  {
    if (!$this->clih) return $default;

    $this->yellow($message . (empty($default) ? '' : ' (press Enter for \'' . $default . '\')') . ': ');

    $input = $this->readRaw();
    if (empty($input)) $input = $default;

    $this->white('  -> ' . $input . "\n");

    return $input;
  }

  public function choose(array $options, string $message, string $default = ''): string
  {
    if (!$this->clih) return $default;

    $this->yellow($message . "\n");
    foreach ($options as $key => $option) {
      $this->white(' ' . (string) $key . ' = ' . (string) $option . "\n");
    }
    $this->yellow('Select one of the options, provide a value' . (empty($default) ? '' : ' or press Enter for \'' . $default . '\'') . ': ');

    $input = $this->readRaw();
    if (is_numeric($input)) $input = (string) ($options[$input] ?? '');
    if (empty($input)) $input = $default;

    $this->white('  -> ' . $input . "\n");

    return $input;
  }

  public function yellow(string $message): void { $this->color('yellow'); echo $message; $this->color('white'); }
  public function green(string $message): void { $this->color('green'); echo $message; $this->color('white'); }
  public function red(string $message): void { $this->color('red'); echo $message; $this->color('white'); }
  public function blue(string $message): void { $this->color('blue'); echo $message; $this->color('white'); }
  public function cyan(string $message): void { $this->color('cyan'); echo $message; $this->color('white'); }
  public function white(string $message): void { $this->color('white'); echo $message; $this->color('white'); }

}