<?php

namespace HubletoMain;

/**
 * Class managing Hubleto releases.
 */
class ReleaseManager
{

  /**
   * @var array{version: string, codename: string}
   */
  protected array $release = ['version' => '', 'codename' => ''];

  /**
   * Class constructor.
   *
   * @param \Hubleto\Framework\Loader $main
   * 
   */
  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  /**
   * Initialization method of the release manager
   *
   * @return void
   * 
   */
  public function init(): void
  {
    $releaseInfoFile = $this->main->config->getAsString('rootFolder') . '/release.json';

    if (@is_file($releaseInfoFile)) {
      $tmp = @json_decode((string) file_get_contents($releaseInfoFile), true) ?? [];
      $this->release = [
        'version' => (string) ($tmp['version'] ?? ''),
        'codename' => (string) ($tmp['codename'] ?? ''),
      ];
    }
  }

  /**
   * Get version of the release
   *
   * @return string
   * 
   */
  public function getVersion(): string
  {
    return $this->release['version'] ?? 'unknown';
  }

  /**
   * Get codename of the release.
   *
   * @return string
   * 
   */
  public function getCodename(): string
  {
    return $this->release['codename'] ?? 'unknown';
  }

}
