<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;

class Template extends \Hubleto\Erp\Model
{
  public string $table = 'documents_templates';
  public string $recordManagerClass = RecordManagers\Template::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible(),
      'used_for' => (new Varchar($this, $this->translate('Used For')))->setRequired()->setDefaultVisible(),
      'content' => (new Text($this, $this->translate('Content')))->setReactComponent('InputTextareaWithHtmlPreview'), // ->setReactComponent('InputWysiwyg'),
      'notes' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // 'Documents';
    $description->ui['addButtonText'] = $this->translate('Add template');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

}
