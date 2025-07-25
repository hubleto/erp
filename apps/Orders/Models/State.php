<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;

class State extends \Hubleto\Framework\Models\Model
{
  public string $table = 'order_states';
  public string $recordManagerClass = RecordManagers\State::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'code' => (new Varchar($this, $this->translate('Code')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
    ]);
  }

}
