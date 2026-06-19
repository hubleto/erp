<?php

namespace Hubleto\App\Community\EmailMarketing\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Mail\Models\Mail;

class Recipient extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_recipients';
  public string $recordManagerClass = RecordManagers\Recipient::class;
  public ?string $lookupSqlValue = '{%TABLE%}.email';

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'EMAIL' => [ self::BELONGS_TO, Email::class, 'id_email', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
    'STATUS' => [ self::BELONGS_TO, RecipientStatus::class, 'email', 'email' ],

    'CLICKS' => [ self::HAS_MANY, EmailClick::class, 'id_recipient', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired()->setReadonly(),
      'id_email' => (new Lookup($this, $this->translate('Email'), Email::class))->setRequired()->setReadonly(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'email' => (new Varchar($this, $this->translate('Email')))->setDefaultVisible(),
      // 'first_name' => (new Varchar($this, $this->translate('First name')))->setDefaultVisible(),
      // 'last_name' => (new Varchar($this, $this->translate('Last name')))->setDefaultVisible(),
      // 'salutation' => (new Varchar($this, $this->translate('Salutation')))->setDefaultVisible(),
      'variables' => (new Json($this, $this->translate('Variables')))->setDefaultVisible()->setReactComponent('InputJsonKeyValue'),
      'date_added' => (new Date($this, $this->translate('Added')))->setDefaultVisible()->setDefaultValue(date('Y-m-d')),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'id_mail' => (new Lookup($this, $this->translate('Reference to mail sent'), Mail::class))->setReadonly(),
      'virt_utm_source' => (new Virtual($this, $this->translate('UTM: source')))
        ->setProperty('sql', "SELECT `e`.`utm_source` FROM `email_marketing_emails` `e` WHERE `e`.`id` = `email_marketing_recipients`.`id_email`"),
      'virt_utm_campaign' => (new Virtual($this, $this->translate('UTM: campaign')))
        ->setProperty('sql', "SELECT `e`.`utm_campaign` FROM `email_marketing_emails` `e` WHERE `e`.`id` = `email_marketing_recipients`.`id_email`"),
      'virt_utm_term' => (new Virtual($this, $this->translate('UTM: term')))
        ->setProperty('sql', "SELECT `e`.`utm_term` FROM `email_marketing_emails` `e` WHERE `e`.`id` = `email_marketing_recipients`.`id_email`"),
      'virt_status' => (new Virtual($this, $this->translate('Status')))
        ->setProperty('sql',"
          SELECT
            concat(if(`is_unsubscribed`, 'unsubscribed', ''), ',', if(`is_invalid`, 'invalid', '')) 
          FROM `email_marketing_recipient_statuses` `crs`
          WHERE `crs`.`email` in (`email_marketing_recipients`.`email`)
        "),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $filters = $this->router()->urlParamAsArray("filters");
    $view = $this->router()->urlParamAsString("view");

    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add recipient');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    if (isset($filters['fGroupBy'])) {
      $description->setPermissions(false, false, false, false);

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

      $description->addColumn(
        'clicks_count',
        (new Integer($this, $this->translate('Clicks')))
      );

      $description->addColumn(
        'clicks_summary',
        (new Text($this, $this->translate('Click summary')))->setTableCssClass('whitespace-pre bg-yellow-50')
      );

      $description->addColumn(
        'bot_score',
        (new Integer($this, $this->translate('Bot score')))
      );

      $description->ui['orderBy'] = [
        'field' => [ 'clicks_count', 'bot_score' ],
        'direction' => [ 'desc', 'asc' ]
      ];

    }

    if ($view != "briefOverview") {
      $description->addFilter('fGroupBy', [
        'title' => $this->translate('Group by'),
        'type' => 'multipleSelectButtons',
        'options' => [
          'id_email' => $this->translate('Email'),
          'virt_utm_source' => $this->translate('UTM: source'),
          'virt_utm_campaign' => $this->translate('UTM: campaign'),
          'virt_utm_term' => $this->translate('UTM: term'),
          'virt_status' => $this->translate('Status'),
          'email' => $this->translate('Email'),
        ]
      ]);
    }

    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['EMAIL'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

}
