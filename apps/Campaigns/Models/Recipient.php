<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Mail\Models\Mail;

class Recipient extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns_recipients';
  public string $recordManagerClass = RecordManagers\Recipient::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired()->setReadonly()->setProperty('defaultVisibility', true),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
      'email' => (new Varchar($this, $this->translate('Email')))->setProperty('defaultVisibility', true),
      'first_name' => (new Varchar($this, $this->translate('First name')))->setProperty('defaultVisibility', true),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setProperty('defaultVisibility', true),
      'salutation' => (new Varchar($this, $this->translate('Salutation')))->setProperty('defaultVisibility', true),
      'id_mail' => (new Lookup($this, $this->translate('Reference to mail sent'), Mail::class))->setReadonly()->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add recipient';
    $description->ui['showHeader'] = true;
    $description->ui['showColumnSearch'] = false;
    $description->ui['showMoreActionsButton'] = false;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    switch ($this->router()->urlParamAsString('view')) {
      case 'briefOverview':
        $description->ui['moreActions'] = null;
        $description->columns = [
          'email' => $description->columns['email'],
          'first_name' => $description->columns['first_name'],
          'last_name' => $description->columns['last_name'],
        ];
      break;
      default:
      break;
    }
    return $description;
  }

}
