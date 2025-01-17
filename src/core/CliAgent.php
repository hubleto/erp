<?php

namespace HubletoMain\Core;

class CliAgent {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function color(string $colorName) {
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

  public function read(string $message, string $default = ''): string {
    global $clih;

    $this->yellow($message . (empty($default) ? '' : ' (press Enter for \'' . $default . '\')') . ': ');

    $input = trim(fgets($clih));
    if (empty($input)) $input = $default;

    $this->white('  -> ' . $input . "\n");

    return $input;
  }

  public function choose(array $options, string $message, string $default = ''): string {
    global $clih;

    $this->yellow($message . "\n");
    foreach ($options as $key => $option) {
      $this->white(' ' . $key . ' = ' . $option . "\n");
    }
    $this->yellow('Select one of the options, provide a value' . (empty($default) ? '' : ' or press Enter for \'' . $default . '\'') . ': ');

    $input = trim(fgets($clih));
    if (is_numeric($input)) $input = $options[$input] ?? '';
    if (empty($input)) $input = $default;

    $this->white('  -> ' . $input . "\n");

    return $input;
  }

  public function yellow(string $message) { $this->color('yellow'); echo $message; $this->color('white'); }
  public function green(string $message) { $this->color('green'); echo $message; $this->color('white'); }
  public function red(string $message) { $this->color('red'); echo $message; $this->color('white'); }
  public function blue(string $message) { $this->color('blue'); echo $message; $this->color('white'); }
  public function cyan(string $message) { $this->color('cyan'); echo $message; $this->color('white'); }
  public function white(string $message) { $this->color('white'); echo $message; $this->color('white'); }

}