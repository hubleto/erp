<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;

class DocumentReview extends \Hubleto\Erp\Model
{
  public string $table = 'documents_reviews';
  public string $recordManagerClass = RecordManagers\DocumentReview::class;
  public ?string $lookupSqlValue = '{%TABLE%}.comments';
  public ?string $lookupUrlAdd = 'documents/reviews/add';
  public ?string $lookupUrlDetail = 'documents/reviews/{%ID%}';

  public array $relations = [
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id'],
    'REQUESTED_BY' => [ self::BELONGS_TO, User::class, 'id_requested_by', 'id'],
    'REVIEWED_BY' => [ self::BELONGS_TO, User::class, 'id_reviewed_by', 'id'],
    'REVIEW_RESULT' => [ self::BELONGS_TO, ReviewResult::class, 'id_review_result', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_document' => (new Lookup($this, $this->translate("Document"), Document::class))->setRequired()->setReadonly(),
      'comment' => (new Varchar($this, $this->translate('Comment')))->setDefaultVisible(),
      'requested_on' => (new DateTime($this, $this->translate('Requested on')))->setReadonly()->setDefaultVisible(),
      'id_requested_by' => (new Lookup($this, $this->translate("Requested by"), User::class))->setDefaultVisible(),
      'reviewed_on' => (new DateTime($this, $this->translate('Reviewed on')))->setReadonly()->setDefaultVisible(),
      'id_reviewed_by' => (new Lookup($this, $this->translate("Reviewed by"), User::class))->setDefaultVisible(),
      'id_review_result' => (new Lookup($this, $this->translate("Review result"), ReviewResult::class))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Request review');
    $description->show(['header', 'fulltextSearch', 'moreActionsButton']);
    $description->hide(['columnSearch', 'footer']);

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    $record['requested_on'] = date('Y-m-d H:i:s');
    $record['id_requested_by'] = $this->authProvider()->getUserId();

    return $record;
  }

  public function getRelationsIncludedInLoadFormData(): array|null
  {
    return ['DOCUMENT'];
  }
}
