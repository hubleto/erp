<?php

namespace HubletoApp\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Lookup;

class ContactTag extends \Hubleto\Framework\Models\Model
{
  public string $table = 'contact_contact_tags';
  public string $recordManagerClass = RecordManagers\ContactTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Contact tags');
    return $description;
  }

}
