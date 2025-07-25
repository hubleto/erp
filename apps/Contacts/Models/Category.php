<?php

namespace HubletoApp\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Category extends \Hubleto\Framework\Models\Model
{
  public string $table = 'contact_categories';
  public string $recordManagerClass = RecordManagers\Category::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Type')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = $this->translate('Contact Categories');
    $description->ui['addButtonText'] = $this->translate('Add Contact Category');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
