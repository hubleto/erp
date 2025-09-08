<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Hubleto\Framework\Helper;

class Dictionary extends \Hubleto\Erp\Controllers\ApiController
{

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function renderJson(): array
  {
    $language = $this->router()->urlParamAsString('language', 'en');
    $addNew = $this->router()->urlParamAsArray('addNew');

    $dictFile = __DIR__ . '/../../lang/' . $language . '.json';

    if ($language == 'en') {
      return [];
    }
    if (!in_array($language, array_keys(\Hubleto\App\Community\Settings\Models\User::ENUM_LANGUAGES))) {
      return [];
    }
    if (!is_file($dictFile)) {
      return [];
    }

    $dict = $this->translator()->loadDictionary($language);

    if (
      isset($addNew['context'])
      && isset($addNew['orig'])
      && $language != 'en'
    ) {

      $tmp = explode('::', $addNew['context']);
      $contextClass = $tmp[0] ?? '';
      $contextInner = $tmp[1] ?? '';

      if (
        !empty($contextClass)
        && class_exists($contextClass)
        && !empty($contextInner)
      ) {
        $contextClass::addToDictionary($language, $contextInner, $addNew['orig']);
      }

      return ['status' => true];
    } else {
      return $dict;
    }
  }

}
