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

  public function color(string $fgColor, string $bgColor = 'black'): void
  {
    if (php_sapi_name() !== 'cli') return;

    $bgSequences = [
      'black' => "\033[40m",
      'red' => "\033[41m",
      'green' => "\033[42m",
      'yellow' => "\033[43m",
      'blue' => "\033[44m",
      'purple' => "\033[45m",
      'cyan' => "\033[46m",
      'white' => "\033[47m",
    ];

    echo $bgSequences[$bgColor] ?? '';

    $fgSequences = [
      'black' => "\033[30m",
      'red' => "\033[31m",
      'green' => "\033[32m",
      'yellow' => "\033[33m",
      'blue' => "\033[34m",
      'purple' => "\033[35m",
      'cyan' => "\033[36m",
      'white' => "\033[37m",
    ];

    echo $fgSequences[$fgColor] ?? '';
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

  public function confirm(string $question): bool
  {
    $answer = $this->read($question);
    return in_array(strtolower($answer), ['yes', 'y', '1']);
  }

  public function yellow(string $message): void { $this->color('yellow'); echo $message; $this->color('white'); }
  public function green(string $message): void { $this->color('green'); echo $message; $this->color('white'); }
  public function red(string $message): void { $this->color('red'); echo $message; $this->color('white'); }
  public function blue(string $message): void { $this->color('blue'); echo $message; $this->color('white'); }
  public function cyan(string $message): void { $this->color('cyan'); echo $message; $this->color('white'); }
  public function white(string $message): void { $this->color('white'); echo $message; $this->color('white'); }

  public function colored(string $bgColor, string $fgColor, string $message): void {
    $this->color($fgColor, $bgColor);
    echo $message;
    $this->color('white', 'black');
    echo "\n";
  }


  public function insertCodeToFile(string $file, string $tag, array $codeLines): bool
  {
    $inserted = false;

    if (!is_file($file)) return false;

    $lines = file($file);
    $newLines = [];
    foreach ($lines as $line) {
      $newLines[] = $line;
      if (str_starts_with(trim($line), $tag)) {
        $identSize = strlen($line) - strlen(ltrim($line));
        foreach ($codeLines as $codeLine) {
          $newLines[] = str_repeat(' ', $identSize) . trim($codeLine) . "\n";
        }
        $inserted = true;
      }
    }

    if ($inserted) {
      file_put_contents($file, join("", $newLines));
      $this->yellow("Code inserted into '{$file}' under '{$tag}'.\n");
    }

    return $inserted;
  }

}