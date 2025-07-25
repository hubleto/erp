<?php

namespace HubletoApp\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Boolean;
use HubletoApp\Community\Settings\Models\User;

class Mail extends \Hubleto\Framework\Models\Model
{
  public string $table = 'mails';
  public string $recordManagerClass = RecordManagers\Mail::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';

  // public array $relations = [
  //   'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
  // ];

  public function describeColumns(): array
  {
    $user = $this->main->auth->getUser();
    return array_merge(parent::describeColumns(), [
      // 'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReadonly(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setRequired()->setDefaultValue(1),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'datetime_sent' => (new DateTime($this, $this->translate('Sent')))->setReadonly(),
      'datetime_read' => (new DateTime($this, $this->translate('Read'))),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired()->setCssClass('font-bold'),
      'from' => (new Varchar($this, $this->translate('From')))->setRequired()->setReadonly()->setDefaultValue($user['email'] ?? ''),
      'to' => (new Varchar($this, $this->translate('To')))->setRequired(),
      'cc' => (new Varchar($this, $this->translate('Cc'))),
      'bcc' => (new Varchar($this, $this->translate('Bcc'))),
      'body' => (new Text($this, $this->translate('Body')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color'))),
      'is_draft' => (new Boolean($this, $this->translate('Draft')))->setDefaultValue(true),
      'is_template' => (new Boolean($this, $this->translate('Template'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $folder = $this->main->urlParamAsString('folder');

    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'New message';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['body']);
    unset($description->columns['color']);
    unset($description->columns['priority']);
    unset($description->columns['read']);

    switch ($folder) {
      case 'inbox':
        unset($description->columns['to']);
        unset($description->columns['cc']);
        unset($description->columns['bcc']);
        break;
      case 'sent':
        unset($description->columns['from']);
        break;
    }

    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;
    $description->ui['addButtonText'] = 'Save draft';
    $description->ui['saveButtonText'] = 'Save draft';

    return $description;
  }

}
