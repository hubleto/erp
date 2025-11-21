<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Mail\Models\Mail;

class RecipientStatus extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns_recipient_statuses';
  public string $recordManagerClass = RecordManagers\RecipientStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.email';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'email' => (new Varchar($this, $this->translate('Email')))->setDefaultVisible()->addIndex('INDEX `email` (`email`)'),
      'is_opted_out' => (new Boolean($this, $this->translate('Is opted-out')))->setDefaultVisible(),
      'is_invalid' => (new Boolean($this, $this->translate('Is invalid')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add recipient status';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $view = $this->router()->urlParamAsString('view');
    if ($view == 'briefOverview') {
      $description->showOnlyColumns(['email', 'is_opted_out', 'is_invalid']);
    }

    return $description;
  }

}
