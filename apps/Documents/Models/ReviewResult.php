<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Varchar;

class ReviewResult extends \Hubleto\Erp\Model
{
  public string $table = 'documents_review_results';
  public string $recordManagerClass = RecordManagers\ReviewResult::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlAdd = 'documents/review-results/add';
  public ?string $lookupUrlDetail = 'documents/review-results/{%ID%}';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible(),
      'description' => (new Varchar($this, $this->translate('Description')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add review result');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

}
