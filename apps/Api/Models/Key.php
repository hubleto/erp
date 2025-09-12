<?php

namespace Hubleto\App\Community\Api\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;

class Key extends \Hubleto\Erp\Model
{
  public string $table = 'api_keys';
  public string $recordManagerClass = RecordManagers\Key::class;
  public ?string $lookupSqlValue = 'concat(substr({%TABLE%}.key, 1, 8), " ...")';
  public ?string $lookupUrlDetail = 'api/keys/{%ID%}';

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'key' => (new Varchar($this, $this->translate('Key')))->setProperty('defaultVisibility', true)->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'valid_until' => (new DateTime($this, $this->translate('Valid until')))->setProperty('defaultVisibility', true)->setDefaultValue(date("Y-m-d H:i:s", strtotime("+14 days"))),
      'is_enabled' => (new Boolean($this, $this->translate('Enabled')))->setDefaultValue(true)->setProperty('defaultVisibility', true),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'ip_address_blacklist' => (new Varchar($this, $this->translate('IP Address blacklist'))),
      'ip_address_whitelist' => (new Varchar($this, $this->translate('IP Address whitelist'))),
      'id_created_by' => (new Lookup($this, $this->translate('Created by'), User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue($this->getService(AuthProvider::class)->getUserId()),
      'created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
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
    $description->ui['addButtonText'] = 'Add Key';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

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
    return parent::describeForm();
  }

  // /**
  //  * [Description for onBeforeCreate]
  //  *
  //  * @param array $record
  //  * 
  //  * @return array
  //  * 
  //  */
  // public function onBeforeCreate(array $record): array
  // {
  //   return parent::onBeforeCreate($record);
  // }

  // public function onBeforeUpdate(array $record): array
  // {
  //   return parent::onBeforeUpdate($record);
  // }

  // public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  // {
  //   return parent::onAfterUpdate($originalRecord, $savedRecord);
  // }

  // public function onAfterCreate(array $savedRecord): array
  // {
  //   return parent::onAfterCreate($savedRecord);
  // }

}
