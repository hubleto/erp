<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class TeamMember extends \Hubleto\Framework\Models\Model
{
  public string $table = 'teams_members';
  public string $recordManagerClass = RecordManagers\TeamMember::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_team' => (new Lookup($this, $this->translate("Team"), Team::class))->setRequired(),
      'id_member' => (new Lookup($this, $this->translate("Member"), User::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add member to team';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = false;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['id_team']);

    return $description;
  }
}
