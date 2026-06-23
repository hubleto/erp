<?php

namespace Hubleto\App\Community\EmailMarketing\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Db\Column\Integer;

class EmailClick extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_email_clicks';
  public string $recordManagerClass = RecordManagers\EmailClick::class;

  public array $relations = [
    'EMAIL' => [ self::BELONGS_TO, Email::class, 'id_email', 'id' ],
    'RECIPIENT' => [ self::HAS_ONE, Recipient::class, 'id_recipient', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_email' => (new Lookup($this, $this->translate('Email'), Email::class))->setRequired(),
      'id_recipient' => (new Lookup($this, $this->translate('Recipient'), Recipient::class))->setDefaultVisible(),
      'url' => (new Varchar($this, $this->translate('Url')))->setDefaultVisible(),
      'datetime_clicked' => (new DateTime($this, $this->translate('Clicked')))->setDefaultVisible(),
      'log' => (new Json($this, $this->translate('Log'))),
      'bot_score' => (new Integer($this, $this->translate('Bot Score')))->setDefaultVisible(),
      'virt_campaign' => (new Virtual($this, $this->translate('Campaign')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT
            group_concat(`c`.`title`)
          FROM `email_marketing_campaigns_schedule` `cs`
          LEFT JOIN `email_marketing_campaigns` `c` on `c`.`id` = `cs`.`id_campaign`
          WHERE `cs`.`id_email` = `email_marketing_email_clicks`.`id_email`
        "),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->permissions['canCreate'] = false;
    $description->permissions['canModify'] = false;
    $description->permissions['canDelete'] = false;

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

}
