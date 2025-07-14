<?php

namespace HubletoMain\Core;

class CronManager
{

  public \HubletoMain $main;

  /** @var array<\HubletoMain\Core\Cron> */
  protected array $crons = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function init(): void
  {
    $crons = @\ADIOS\Core\Helper::scanDirRecursively($this->main->config->getAsString('srcFolder') . '/crons');
    foreach ($crons as $cron) {
      $cronClass = '\\HubletoMain\\Cron\\' . str_replace('/', '\\', $cron);
      $cronClass = str_replace('.php', '', $cronClass);
      $this->addCron($cronClass);
    }

    $crons = @\ADIOS\Core\Helper::scanDirRecursively($this->main->config->getAsString('rootFolder') . '/crons');
    foreach ($crons as $cron) {
      $cronClass = '\\HubletoCustom\\Cron\\' . str_replace('/', '\\', $cron);
      $cronClass = str_replace('.php', '', $cronClass);
      $this->addCron($cronClass);
    }
  }

  public function log(string $msg): void
  {
    $this->main->logger->info($msg);
  }

  public function addCron(string $cronClass): void
  {
    $this->crons[$cronClass] = new $cronClass($this->main);
  }

  public function getCrons(): array
  {
    return $this->crons;
  }

  public function run(): void
  {
    foreach ($this->getCrons() as $cronClass => $cron) {
      $schedule = explode(' ', $cron->schedulingPattern);

      $minNow = (int) date('i');
      $hourNow = (int) date('H');
      $dayNow = (int) date('d');
      $monthNowh = (int) date('m');
      $dowNow = (int) (date('N') - 1);

      $minSchedule = trim($schedule[0]);
      $hourSchedule = trim($schedule[1]);
      $daySchedule = trim($schedule[2]);
      $monthSchedule = trim($schedule[3]);
      $dowSchedule = trim($schedule[4]);

      $minMatch = false;
      $hourMatch = false;
      $dayMatch = false;
      $monthMatch = false;
      $dowMatch = false;

      if ($minSchedule == '*') $minMatch = true;
      else if (str_starts_with($minSchedule, '*/')) $minMatch = $minNow % ((int) str_replace('*/', '', $minSchedule)) == 0;
      else $minMatch = $minNow == (int) $minSchedule;

      if ($hourSchedule == '*') $hourMatch = true;
      else if (str_starts_with($hourSchedule, '*/')) $hourMatch = $hourNow % ((int) str_replace('*/', '', $hourSchedule)) == 0;
      else $hourMatch = $hourNow == (int) $hourSchedule;

      if ($daySchedule == '*') $dayMatch = true;
      else if (str_starts_with($daySchedule, '*/')) $dayMatch = $dayNow % ((int) str_replace('*/', '', $daySchedule)) == 0;
      else $dayMatch = $dayNow == (int) $daySchedule;

      if ($monthSchedule == '*') $monthMatch = true;
      else if (str_starts_with($monthSchedule, '*/')) $monthMatch = $monthNow % ((int) str_replace('*/', '', $monthSchedule)) == 0;
      else $monthMatch = $monthNow == (int) $monthSchedule;

      if ($dowSchedule == '*') $dowMatch = true;
      else if (str_starts_with($dowSchedule, '*/')) $dowMatch = $dowNow % ((int) str_replace('*/', '', $dowSchedule)) == 0;
      else $dowMatch = $dowNow == (int) $dowSchedule;

      if ($minMatch && $hourMatch && $dayMatch && $monthMatch && $dowMatch) {
        $this->log('Starting `' . $cronClass . '`.');
        $cron->run();
      }
    }
  }

}