<?php

namespace HubletoApp\Community\Notifications\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Json;
use HubletoApp\Community\Settings\Models\User;

class Notification extends \Hubleto\Framework\Models\Model
{
  public string $table = 'notifications';
  public string $recordManagerClass = RecordManagers\Notification::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';

  private static array $categories = [
    0 => 'No category'
  ];

  public array $relations = [
    'FROM' => [ self::BELONGS_TO, User::class, 'id_from', 'id' ],
    'TO' => [ self::BELONGS_TO, User::class, 'id_to', 'id' ],
  ];

  public static function addCategory(int $id, string $category): bool
  {
    if (!isset(self::$categories[$id])) {
      self::$categories[$id] = $category;
      return true;
    } else {
      return false;
    }
  }

  public static function getCategories(): array
  {
    return self::$categories;
  }

  public function describeColumns(): array
  {
    $user = $this->main->auth->getUser();
    return array_merge(parent::describeColumns(), [
      'priority' => (new Integer($this, $this->translate('Priority')))->setRequired()->setDefaultValue(1),
      'category' => (new Integer($this, $this->translate('Category')))->setRequired()->setEnumValues(self::getCategories()),
      'id_from' => (new Lookup($this, $this->translate('From'), User::class))->setRequired(),
      'id_to' => (new Lookup($this, $this->translate('To'), User::class))->setRequired(),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired()->setCssClass('font-bold'),
      'body' => (new Text($this, $this->translate('Body')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color'))),
      'tags' => (new Json($this, $this->translate('Tags'))),
      'datetime_sent' => (new DateTime($this, $this->translate('Sent')))->setRequired()->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'datetime_read' => (new DateTime($this, $this->translate('Read'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $folder = $this->main->urlParamAsString('folder');

    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Send message';
    $description->ui['showHeader'] = false;
    $description->ui['showFulltextSearch'] = false;
    $description->ui['showColumnSearch'] = false;
    $description->ui['showFooter'] = false;

    unset($description->columns['body']);
    unset($description->columns['color']);
    unset($description->columns['priority']);
    unset($description->columns['read']);

    switch ($folder) {
      case 'inbox':
        unset($description->columns['id_to']);
        break;
      case 'sent':
        unset($description->columns['id_from']);
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

    return $description;
  }

}
