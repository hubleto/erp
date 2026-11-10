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
    'RECIPIENTS' => [ self::HAS_MANY, CampaignScheduleRecipient::class, 'id_campaign_schedule', 'id' ],
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
    $description->ui['addButtonText'] = $this->translate('Add email');
    $description->setOrderBy('day', 'asc');
    $description->show(['header']);
    $description->hide(['footer']);

    return $description;
  }

  /**
   * [Description for getRelationsIncludedInLoadTableData]
   *
   * @return array|null
   * 
   */
  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['CAMPAIGN', 'EMAIL', 'RECIPIENTS', 'EMAIL.SENDER_ACCOUNT', 'EMAIL.RECIPIENTS'];
  }

  /**
   * [Description for getMaxReadLevelForLoadTableData]
   *
   * @return int
   * 
   */
  public function getMaxReadLevelForLoadTableData(): int
  {
    return 3;
  }

  /**
   * [Description for getRelationsIncludedInLoadFormData]
   *
   * @return array|null
   * 
   */
  public function getRelationsIncludedInLoadFormData(): array|null
  {
    return ['CAMPAIGN', 'EMAIL', 'RECIPIENTS', 'EMAIL.SENDER_ACCOUNT', 'EMAIL.RECIPIENTS'];
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


  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {

    $idCampaign = $savedRecord['id_campaign'] ?? 0;

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    if ($idCampaign > 0) {
      $campaign = $mCampaign->record->where('id', $idCampaign)->first();
      $email = $mEmail->record->recordCreate([
        'title' => $campaign->title,
        'mail_subject' => $campaign->title,
        'mail_body' => $campaign->mail_body_template,
      ]);

      $idEmail = $email['id'] ?? 0;

      $this->record->where('id', $savedRecord['id'] ?? 0)->update([
        'id_email' => $idEmail
      ]);
    }

    return parent::onAfterCreate($savedRecord);
  }
  
}
