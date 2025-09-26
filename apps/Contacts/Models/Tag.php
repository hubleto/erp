<?php

namespace Hubleto\App\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;

class Tag extends \Hubleto\Erp\Model
{
  public string $table = 'contact_tags';
  public string $recordManagerClass = RecordManagers\Tag::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlAdd = 'contacts/tags/add';
  public ?string $lookupUrlDetail = 'contacts/tags/{%ID%}';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = $this->translate('Contact Tags');
    $description->ui['addButtonText'] = $this->translate('Add Contact Tag');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
