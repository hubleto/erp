<?php

namespace HubletoApp\Community\Messages\Models;

use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Color;

class Message extends \HubletoMain\Core\Models\Model
{
  public string $table = 'messages';
  public string $recordManagerClass = RecordManagers\Message::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'from' => (new Varchar($this, $this->translate('From')))->setRequired(),
      'to' => (new Varchar($this, $this->translate('To')))->setRequired(),
      'cc' => (new Varchar($this, $this->translate('CC'))),
      'bcc' => (new Varchar($this, $this->translate('BCC'))),
      'subject' => (new Varchar($this, $this->translate('Name'))),
      'body' => (new Text($this, $this->translate('Body')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add Message';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
