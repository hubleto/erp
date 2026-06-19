<?php

namespace Hubleto\App\Community\EmailMarketing\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Integer;

class CampaignSchedule extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_campaigns_schedule';
  public string $recordManagerClass = RecordManagers\CampaignSchedule::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'EMAIL' => [ self::BELONGS_TO, Email::class, 'id_email', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setReadonly()->setRequired(),
      'day' => (new Integer($this, $this->translate('Day')))->setDefaultVisible(),
      'id_email' => (new Lookup($this, $this->translate('Email'), Email::class))->setDefaultVisible(),
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
    $description->ui['addButtonText'] = 'Add schedule';
    $description->setOrderBy('day', 'asc');
    $description->show(['header']);
    $description->hide(['footer']);

    return $description;
  }


  /**
   * [Description for getRelationsIncludedInLoadFormData]
   *
   * @return array|null
   * 
   */
  public function getRelationsIncludedInLoadFormData(): array|null
  {
    return ['CAMPAIGN', 'EMAIL', 'EMAIL.SENDER_ACCOUNT'];
  }


  /**
   * [Description for getMaxReadLevelForLoadFormData]
   *
   * @return int
   * 
   */
  public function getMaxReadLevelForLoadFormData(): int
  {
    return 2;
  }

}
