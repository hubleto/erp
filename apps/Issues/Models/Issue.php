<?php

namespace Hubleto\App\Community\Issues\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Issue extends \Hubleto\Erp\Model
{
  public string $table = 'issues';
  public string $recordManagerClass = RecordManagers\Issue::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'problem' => (new Varchar($this, $this->translate('Problem description')))->setRequired(),
    ]);
  }

}
