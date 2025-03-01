<?php

namespace HubletoApp\Community\CalendarSync\Models\Eloquent;

class Source extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'source';

  public function prepareReadQuery(): mixed
  {

    $main = \ADIOS\Core\Helper::getGlobalApp();

    $query = parent::prepareReadQuery();
    $type = $main->urlParamAsString('type') ?? "";

    if ($type == 'google') {
      $query = $query->where('type', 'google');
    }
    else if ($type == 'ics') {
      $query = $query->where('type', 'ics');
    }

    return $query;
  }
}