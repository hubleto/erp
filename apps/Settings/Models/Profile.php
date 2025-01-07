<?php

namespace HubletoApp\Settings\Models;

class Profile extends \HubletoMain\Core\Model
{
  public string $table = 'profiles';
  public string $eloquentClass = Eloquent\Setting::class;
  public ?string $lookupSqlValue = '{%TABLE%}.company';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'company' => [
        'type' => 'varchar',
        'byte_size' => '250',
        'title' => $this->translate('Company'),
        'show_column' => true
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Profiles';
    $description['ui']['addButtonText'] = 'Add Profile';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
