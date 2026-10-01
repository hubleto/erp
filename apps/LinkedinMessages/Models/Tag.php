<?php

namespace Hubleto\App\Community\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;

class Tag extends \Hubleto\Erp\Model
{
  public string $table = 'linkedin_tags';
  public string $recordManagerClass = RecordManagers\Tag::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'linkedin-messages/tags/{%ID%}';

  public array $relations = [
    'MESSAGE_TAGS' => [ self::HAS_MANY, MessageTag::class, 'id_tag', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired()->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Message tags');
    $description->ui['addButtonText'] = $this->translate('Add tag');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add tag');
    return $description;
  }

  public function onBeforeDelete(int $id): int
  {
    $mMessageTag = $this->getModel(MessageTag::class);
    $mMessageTag->record->where('id_tag', $id)->delete();
    return parent::onBeforeDelete($id);
  }
}
