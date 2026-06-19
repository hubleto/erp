<?php

namespace Hubleto\App\Community\EmailMarketing\Models;

use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Integer;

class CampaignScheduleRecipient extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_campaigns_schedule_recipients';
  public string $recordManagerClass = RecordManagers\CampaignScheduleRecipient::class;

  public array $relations = [
    'CAMPAIGN_SCHEDULE' => [ self::BELONGS_TO, CampaignSchedule::class, 'id_schedule', 'id' ],
    'RECIPIENT' => [ self::BELONGS_TO, Recipient::class, 'id_recipient', 'id' ],
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign_schedule' => (new Lookup($this, $this->translate('Campaign schedule'), CampaignSchedule::class))->setReadonly()->setRequired(),
      'id_recipient' => (new Lookup($this, $this->translate('Recipient'), Recipient::class))->setDefaultVisible(),
      'id_mail' => (new Lookup($this, $this->translate('Reference to mail sent'), Mail::class))->setDefaultVisible(),
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
    $description->ui['addButtonText'] = 'Add recipient';
    $description->show(['header']);
    $description->hide(['footer']);

    return $description;
  }

}
