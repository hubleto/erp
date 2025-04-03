<?php

namespace HubletoMain\Cli\Agent\Release;

class Create extends \HubletoMain\Cli\Agent\Command
{
  public array $releaseConfig = [];

  public function addFolderToZip($zip, string $path, string $relativePathPrefix)
  {
    /** @var SplFileInfo[] $files */
    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::FOLLOW_SYMLINKS), \RecursiveIteratorIterator::LEAVES_ONLY);

    foreach ($files as $file) {
      if ($file->isDir()) continue;
      if (strpos($file, '.git') !== false) continue;
      if (strpos($file, 'node_modules') !== false) continue;
      $filePath = str_replace('\\', '/', $file->getRealPath());
      $relativePath = substr($filePath, strlen($path) + 1);

      $zip->addFile($filePath, $relativePathPrefix . '/' . $relativePath);
    }
  }

  public function run(): void
  {

    $hubletoRootFolder = realpath(__DIR__ . '/../../../..');

    $this->cli->red("\n");
    $this->cli->red("!!! WARNING !!! Script does not support symbolic links. Check your composer libraries ('vendor' folder).\n");
    $this->cli->red("\n");

    $zipFilePath = pathinfo(get_included_files()[0], PATHINFO_DIRNAME) . '/hubleto-' . $this->main->release->getVersion() . '-ce.zip';
    $zipFilePath = $this->cli->read('Full path to .zip file (File will be overwritten !!!)', $zipFilePath);

    $zip = new \ZipArchive();
    $zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    $this->addFolderToZip($zip, $hubletoRootFolder . '/apps', 'apps');
    $this->addFolderToZip($zip, $hubletoRootFolder . '/assets', 'assets');
    $this->addFolderToZip($zip, $hubletoRootFolder . '/lang', 'lang');
    $this->addFolderToZip($zip, $hubletoRootFolder . '/src', 'src');
    $this->addFolderToZip($zip, $hubletoRootFolder . '/vendor', 'vendor');

    $zip->addFile($hubletoRootFolder . '/.htaccess', '.htaccess');
    $zip->addFile($hubletoRootFolder . '/hubleto', 'hubleto');
    $zip->addFile($hubletoRootFolder . '/LICENSE', 'LICENSE');
    $zip->addFile($hubletoRootFolder . '/README.md', 'README.md');
    $zip->addFile($hubletoRootFolder . '/index.php', 'index.php');

    // Zip archive will be created only after closing object
    $zip->close();

  }
}