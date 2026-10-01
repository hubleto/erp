<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\Lookup;

class MessageTag extends \Hubleto\Erp\Model
{
  public string $table = 'cross_linkedin_message_tags';
  public string $recordManagerClass = RecordManagers\MessageTag::class;
  public ?string $lookupSqlValue = '`{%TABLE%}`.id';
  public ?string $lookupUrlDetail = 'linkedin-messages/{%ID%}';

  public array $relations = [
    'MESSAGE' => [ self::BELONGS_TO, Message::class, 'id_message', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_message' => (new Lookup($this, $this->translate('Message'), Message::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Message tags');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
  }
}
