<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;

class Level extends \HubletoMain\Model
{
  public string $table = 'lead_levels';
  public string $recordManagerClass = RecordManagers\Level::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

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

    $description->ui['title'] = 'Lead Levels';
    $description->ui['addButtonText'] = 'Add Lead Level';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->ui['title'] = 'Lead level';
    $description->ui['templateJson'] = json_encode([
      'tabs' => [
        'default' => [
          'form.columns' => [ 'columns' => [
            'form.column#left' => [ 'items' => [
              'form.divider' => ['text' => 'Lead level'],
              'form.input#name' => ['input' => 'name'],
            ] ],
            'form.column#right' => [ 'items' => [
              'form.divider' => ['text' => 'Color'],
              'form.input#color' => ['input' => 'color'],
            ] ],
          ] ],
        ],
      ],
    ]);

    return $description;
  }

}
