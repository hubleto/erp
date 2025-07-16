<?php

namespace HubletoMain\Core\Api;

use \ADIOS\Core\Helper;

class Dictionary extends \HubletoMain\Core\Controllers\ApiController
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
      list($contextClass, $contextInner) = explode('::', $addNew['context']);
      $contextClass::addToDictionary($language, $contextInner, $addNew['orig']);
      return ['status' => true];
    } else {
      return $dict;
    }
  }

}
