<?php

namespace Hubleto\App\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Scope extends \Hubleto\Erp\Model
{
  public string $table = 'oauth_scopes';
  public string $recordManagerClass = RecordManagers\Scope::class;
  public ?string $lookupSqlValue = 'concat("Scope #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'scope_id' => (new Varchar($this, $this->translate('Scope Id')))->setDefaultVisible(),
      'description' => (new Varchar($this, $this->translate('Description')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Scope';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
