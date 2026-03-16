<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Mail\Models\Mail;

class Recipient extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns_recipients';
  public string $recordManagerClass = RecordManagers\Recipient::class;
  public ?string $lookupSqlValue = '{%TABLE%}.email';

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
    'STATUS' => [ self::BELONGS_TO, RecipientStatus::class, 'email', 'email' ],

    'CLICKS' => [ self::HAS_MANY, Click::class, 'id_recipient', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired()->setReadonly()->setDefaultVisible(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'email' => (new Varchar($this, $this->translate('Email')))->setDefaultVisible(),
      'first_name' => (new Varchar($this, $this->translate('First name')))->setDefaultVisible(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setDefaultVisible(),
      'salutation' => (new Varchar($this, $this->translate('Salutation')))->setDefaultVisible(),
      'variables' => (new Json($this, $this->translate('Variables')))->setDefaultVisible()->setReactComponent('InputJsonKeyValue'),
      'id_mail' => (new Lookup($this, $this->translate('Reference to mail sent'), Mail::class))->setReadonly()->setDefaultVisible(),
      'virt_utm_source' => (new Virtual($this, $this->translate('UTM: Source')))->setDefaultVisible()
        ->setProperty('sql', "SELECT `c`.`utm_source` FROM `campaigns` `c` WHERE `c`.`id` = `campaigns_recipients`.`id_campaign`"),
      'virt_utm_campaign' => (new Virtual($this, $this->translate('UTM: Campaign')))->setDefaultVisible()
        ->setProperty('sql', "SELECT `c`.`utm_campaign` FROM `campaigns` `c` WHERE `c`.`id` = `campaigns_recipients`.`id_campaign`"),
      'virt_utm_term' => (new Virtual($this, $this->translate('UTM: Term')))->setDefaultVisible()
        ->setProperty('sql', "SELECT `c`.`utm_term` FROM `campaigns` `c` WHERE `c`.`id` = `campaigns_recipients`.`id_campaign`"),
      'virt_status' => (new Virtual($this, $this->translate('Status')))->setDefaultVisible()
        ->setProperty('sql',"
          SELECT
            concat(if(`is_unsubscribed`, 'unsubscribed', ''), ',', if(`is_invalid`, 'invalid', '')) 
          FROM `campaigns_recipient_statuses` `crs`
          WHERE `crs`.`email` in (`campaigns_recipients`.`email`)
        "),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $filters = $this->router()->urlParamAsArray("filters");

    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add recipient';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];

      $showOnlyColumns = [];
      if (in_array('virt_utm_source', $fGroupBy)) $showOnlyColumns[] = 'virt_utm_source';
      if (in_array('virt_utm_campaign', $fGroupBy)) $showOnlyColumns[] = 'virt_utm_campaign';
      if (in_array('virt_utm_term', $fGroupBy)) $showOnlyColumns[] = 'virt_utm_term';
      if (in_array('virt_status', $fGroupBy)) $showOnlyColumns[] = 'virt_status';
      if (in_array('email', $fGroupBy)) $showOnlyColumns[] = 'email';

      $description->showOnlyColumns($showOnlyColumns);

      $description->addColumn(
        'count',
        (new Integer($this, $this->translate('Count')))
      );

    }

    $description->addFilter('fGroupBy', [
      'title' => $this->translate('Group by'),
      'type' => 'multipleSelectButtons',
      'options' => [
        'id_campaign' => $this->translate('Campaign'),
        'virt_utm_source' => $this->translate('UTM: source'),
        'virt_utm_campaign' => $this->translate('UTM: campaign'),
        'virt_utm_term' => $this->translate('UTM: term'),
        'virt_status' => $this->translate('Status'),
        'email' => $this->translate('Email'),
      ]
    ]);

    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['CAMPAIGN'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

}
