<?php

namespace HubletoApp\Community\Settings\Models;

class Profile extends \HubletoMain\Core\Model
{
  public string $table = 'profiles';
  public string $eloquentClass = Eloquent\Profile::class;
  public ?string $lookupSqlValue = '{%TABLE%}.company';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy([
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
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Profiles';
      $description['ui']['addButtonText'] = 'Add Profile';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }

}
