<?php

namespace HubletoApp\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;

class Template extends \HubletoMain\Model
{
  public string $table = 'documents_templates';
  public string $recordManagerClass = RecordManagers\Template::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'content' => (new Text($this, $this->translate('Content'))), // ->setReactComponent('InputWysiwyg'),
      'notes' => (new Text($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // 'Documents';
    $description->ui['addButtonText'] = $this->translate('Add template');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;

    return $description;
  }

}
