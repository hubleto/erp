<?php

namespace Hubleto\App\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Documents\Models\Document;

class LeadDocument extends \Hubleto\Erp\Model
{
  public string $table = 'lead_documents';
  public string $recordManagerClass = RecordManagers\LeadDocument::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class, "CASCADE"))->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class, "CASCADE"))->setRequired(),
    ]);
  }

  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'hyperlink':
        $description->setReactComponent('InputHyperlink');
        break;
    }
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    if ($this->router()->urlParamAsInteger('idLead') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }

  public function onBeforeDelete(int $id): int
  {
    $idDocument = (int) $this->record->find($id)->toArray()["id_document"];
    ($this->getService(Document::class))->onBeforeDelete($idDocument);
    ($this->getService(Document::class))->record->where("id", $idDocument)->delete();

    return $id;
  }
}
