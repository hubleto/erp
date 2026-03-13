<?php

namespace Hubleto\App\Community\AuditLogs\Models;


use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Auth\Models\User;


class AuditLog extends \Hubleto\Erp\Model
{
  const TYPE_CREATE = 1;
  const TYPE_UPDATE = 2;
  const TYPE_DELETE = 3;

  const ENUM_TYPES = [
    self::TYPE_CREATE => 'create',
    self::TYPE_UPDATE => 'update',
    self::TYPE_DELETE => 'delete',
  ];

  const ENUM_TYPES_CSS_CLASSES = [
    self::TYPE_CREATE => 'bg-yellow-300',
    self::TYPE_UPDATE => 'bg-blue-300',
    self::TYPE_DELETE => 'bg-red-300',
  ];

  public string $table = 'audit_logs';
  public string $recordManagerClass = RecordManagers\AuditLog::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.id, " ", {%TABLE%}.type, {%TABLE%}.context, {%TABLE%}.model, {%TABLE%}.message)';

  public bool $disableAuditLog = true;

  public array $relations = [
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'datetime' => (new DateTime($this, $this->translate('Datetime')))->setRequired()->setReadonly()->setDefaultVisible(),
      'type' => (new Integer($this, $this->translate('Type')))->setRequired()->setEnumValues(self::ENUM_TYPES)->setEnumCssClasses(self::ENUM_TYPES_CSS_CLASSES)->setDefaultVisible(),
      'context' => (new Varchar($this, $this->translate('Context')))->setRequired()->setReadonly()->setDefaultVisible(),
      'model' => (new Varchar($this, $this->translate('Model')))->setRequired()->setReadonly()->setDefaultVisible(),
      'record_id' => (new Integer($this, $this->translate('RecordId')))->setRequired()->setDefaultVisible(),
      'message' => (new Varchar($this, $this->translate('Message')))->setRequired()->setReadonly()->setDefaultVisible(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setRequired()->setDefaultVisible(),
      'id_user' => (new Lookup($this, $this->translate('User'), User::class))->setReactComponent('InputUserSelect')->setReadonly()->setRequired()->setDefaultVisible(),
      'ip' => (new Varchar($this, $this->translate('IP')))->setRequired()->setReadonly()->setDefaultVisible(),
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
    $folder = $this->router()->urlParamAsString('folder');

    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->permissions['canCreate'] = false;
    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;
    $description->ui['orderBy'] = 'datetime desc';

    return $description;
  }

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['readonly'] = true;
    $description->permissions['canCreate'] = false;
    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;
    return $description;
  }

}
