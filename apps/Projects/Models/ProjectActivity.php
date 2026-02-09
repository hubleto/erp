<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Contacts\Models\Contact;

class ProjectActivity extends \Hubleto\App\Community\Calendar\Models\Activity
{
  public string $table = 'project_activities';
  public string $recordManagerClass = RecordManagers\ProjectActivity::class;

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
    ]);
  }

}
