<?php

namespace HubletoApp\Community\Messages\Models;

use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\DateTime;
use ADIOS\Core\Db\Column\Lookup;

use HubletoApp\Community\Settings\Models\User;

class Message extends \HubletoMain\Core\Models\Model
{
  public string $table = 'messages';
  public string $recordManagerClass = RecordManagers\Message::class;
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
      'sent' => (new DateTime($this, $this->translate('Sent')))->setRequired()->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'read' => (new DateTime($this, $this->translate('Read'))),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired()->setCssClass('font-bold'),
      'from' => (new Varchar($this, $this->translate('From')))->setRequired()->setReadonly()->setDefaultValue($user['email'] ?? ''),
      'to' => (new Varchar($this, $this->translate('To')))->setRequired(),
      'cc' => (new Varchar($this, $this->translate('Cc'))),
      'bcc' => (new Varchar($this, $this->translate('Bcc'))),
      'body' => (new Text($this, $this->translate('Body')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $folder = $this->main->urlParamAsString('folder');

    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Send message';
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

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;

    return $description;
  }

}
