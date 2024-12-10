<?php

namespace CeremonyCrmApp\Core;

use \ADIOS\Core\Helper;

class Dictionary extends \ADIOS\Core\Controller
{

  public function renderJson(): array
  {
    $language = $this->app->params['language'];

    if (!in_array($language, array_keys(\CeremonyCrmApp\Modules\Core\Settings\Models\User::ENUM_LANGUAGES))) return [];

    $dictFile = __DIR__ . '/../Lang/' . $language . '.json';
    if (!is_file($dictFile)) return [];

    return (array) json_decode(file_get_contents($dictFile), true);
  }

}
