<?php

namespace CeremonyCrmApp\Core;

use \ADIOS\Core\Helper;

class Dictionary extends \ADIOS\Core\Controller
{

  public function renderJson(): array
  {
    $language = $this->app->params['language'];
    $dictFile = __DIR__ . '/../../lang/' . $language . '.json';

    if ($language == 'en') return [];
    if (!in_array($language, array_keys(\CeremonyCrmMod\Core\Settings\Models\User::ENUM_LANGUAGES))) return [];
    if (!is_file($dictFile)) return [];

    $dict = (array) json_decode(file_get_contents($dictFile), true);

    if (is_array($this->app->params['addNew']) && $language != 'en') {
      $context = $this->app->params['addNew']['context'] ?? '';
      $orig = $this->app->params['addNew']['orig'] ?? '';

      if (!empty($orig) && !empty($context)) {
        $dict[$context][$orig] = "";
        file_put_contents($dictFile, json_encode($dict, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
      }

      return ['dictFile' => $dictFile, 'status' => true];
    } else {
      return $dict;
    }
  }

}
