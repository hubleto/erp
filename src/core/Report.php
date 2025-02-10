<?php

namespace HubletoMain\Core;

class Report
{

  public \HubletoMain $main;

  public Model $model;
  public array $returnWith;
  public array $groupsBy;
  public array $fields;

  protected string $urlSlug = '';
  protected string $name = '';

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function getUrlSlug(): string { return $this->urlSlug; }

  public function getReportConfig(): array
  {
    // to be overriden
    return [];
  }

  public function getAllFields(): array {
    $columns = $this->model->getColumns();

    $fields = [];

    foreach ($columns as $key => $column) {
      if ($key == "id") continue;
      $columnDescription = $column->jsonSerialize();
      $fields[$key] = $columnDescription;
    }

    return $fields;
  }
}
