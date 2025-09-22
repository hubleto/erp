<?php

namespace Hubleto\App\Community\Auth\Models;


use Hubleto\Framework\Db\Column\Lookup;

class UserHasRole extends \Hubleto\Erp\Model
{
  public string $table = 'user_has_roles';
  public string $recordManagerClass = RecordManagers\UserHasRole::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('User'), User::class))->setReactComponent('InputUserSelect'),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class)),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    // $description->ui['title'] = 'Role Assigments';
    $description->ui['addButtonText'] = 'Assign Roles';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}
