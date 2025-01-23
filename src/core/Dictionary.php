<?php

namespace HubletoMain\Core;

use \ADIOS\Core\Helper;

class Dictionary extends \ADIOS\Core\Controller
{

  public \HubletoMain $main;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function renderJson(): array
  {
    $language = $this->main->urlParamAsString('language', 'en');
    $addNew = $this->main->urlParamAsArray('addNew');

    $dictFile = __DIR__ . '/../../lang/' . $language . '.json';

    if ($language == 'en') return [];
    if (!in_array($language, array_keys(\HubletoApp\Community\Settings\Models\User::ENUM_LANGUAGES))) return [];
    if (!is_file($dictFile)) return [];

    $dict = $this->main->translator->loadDictionary($language);

    if (isset($addNew['context']) && isset($addNew['orig']) && $language != 'en') {
      $this->main->translator->addToDictionary($addNew['orig'], $addNew['context'], $language);
      return ['status' => true];
    } else {
      return $dict;
    }
  }

}
