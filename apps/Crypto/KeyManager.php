<?php

namespace HubletoApp\Community\Crypto;

use Defuse\Crypto\File;
use Defuse\Crypto\Key;

class KeyManager
{

  public static function getKeyFileName(): string
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $cryptoApp = $main->apps->community('Crypto');
    $fileName = $cryptoApp->configAsString('keyFileName');

    if (empty($fileName)) {
      $fileName = $main->secureFolder . '/hubleto-key-' . rand(1000, 9999) . rand(1000, 9999);
      $cryptoApp->saveConfig('keyFileName', $fileName);
    }

    return $fileName;
  }

  public static function getKey(): Key
  {
    $keyFileName = self::getKeyFileName();

    if (!is_file($keyFileName)) {
      file_put_contents($keyFileName, Key::createNewRandomKey()->saveToAsciiSafeString());
    }

    return Key::loadFromAsciiSafeString(file_get_contents($keyFileName));
  }

  public static function encryptString(string $original): string
  {
    $key = self::getKey();

    $inputHandle = fopen('data://text/plain;base64,' . base64_encode($original), 'r');
    $outputHandle = fopen('php://temp', 'rw');

    File::encryptResource($inputHandle, $outputHandle, $key);

    rewind($outputHandle);
    $encrypted = stream_get_contents($outputHandle);

    fclose($inputHandle);
    fclose($outputHandle);

    return base64_encode($encrypted);

  }

  public static function decryptString(string $original): string
  {
    $key = self::getKey();

    $inputHandle = fopen('data://text/plain;base64,' . $original, 'r');
    $outputHandle = fopen('php://temp', 'rw');

    File::decryptResource($inputHandle, $outputHandle, $key);

    rewind($outputHandle);
    $decrypted = stream_get_contents($outputHandle);

    fclose($inputHandle);
    fclose($outputHandle);

    return $decrypted;

  }

}
