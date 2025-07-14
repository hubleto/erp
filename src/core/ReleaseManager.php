<?php

namespace HubletoMain\Core;

class ReleaseManager
{

  public \HubletoMain $main;

  /** @var array<string, mixed> */
  protected array $release = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function init(): void
  {
    $releaseInfoFile = $this->main->config->getAsString('rootFolder') . '/release.json';

    if (@is_file($releaseInfoFile)) {
      $this->release = @json_decode(file_get_contents($releaseInfoFile), true) ?? [];
    }
  }

  public function getVersion(): string
  {
    return $this->release['version'] ?? 'unknown';
  }

  public function getCodename(): string
  {
    return $this->release['codename'] ?? 'unknown';
  }

}