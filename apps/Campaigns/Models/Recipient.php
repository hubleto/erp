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
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
      'email' => (new Varchar($this, $this->translate('Recipient\'s email'))),
      'id_mail' => (new Lookup($this, $this->translate('Reference to mail sent'), Mail::class)),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add recipient';
    switch ($this->router()->urlParamAsString('view')) {
      case 'briefOverview':
        $description->ui['showHeader'] = false;
        $description->ui['showColumnSearch'] = false;
        $description->ui['showFulltextSearch'] = false;
        $description->ui['showFooter'] = false;
        $description->columns = [
          'id_contact' => $description->columns['id_contact'],
          'email' => $description->columns['email'],
        ];
      break;
      default:
      break;
    }
    return $description;
  }

}
