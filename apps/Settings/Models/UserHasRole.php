<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Lookup;

class UserHasRole extends \HubletoMain\Model
{
  public string $table = 'user_has_roles';
  public string $recordManagerClass = RecordManagers\UserHasRole::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('User'), User::class)),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class)),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    // $description->ui['title'] = 'Role Assigments';
    $description->ui['addButtonText'] = 'Assign Roles';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = false;
    $description->ui['showColumnSearch'] = false;
    $description->ui['showFooter'] = false;

    return $description;
  }
}
