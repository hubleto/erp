<?php

namespace HubletoApp\Community\CalendarSync\Models;

class Source extends \HubletoMain\Core\Model
{
  public string $table = 'sources';
  public string $eloquentClass = \HubletoApp\Community\CalendarSync\Models\Eloquent\Source::class;

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'link' => [
        'type' => 'varchar',
        'title' => 'Calendar link',
        'required' => true
      ],
      'type' => [
        'type' => 'varchar',
        'title' => $this->translate('Type'),
        'enumValues' => ['google' => "Google Calendar"],
        'required' => true,
      ]
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Calendar sources';
    $description['ui']['addButtonText'] = 'Add calendar source';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
