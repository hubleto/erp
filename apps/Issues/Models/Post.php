<?php

namespace Hubleto\App\Community\Issues\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Mail\Models\Mail;

class Post extends \Hubleto\Erp\Model
{
  public string $table = 'issues_posts';
  public string $recordManagerClass = RecordManagers\Post::class;

  public array $relations = [
    'ISSUE' => [ self::BELONGS_TO, Issue::class, 'id_issue', 'id' ],
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_issue' => (new Lookup($this, $this->translate('Issue'), Issue::class))->setRequired()->setDefaultVisible(),
      'from' => (new Varchar($this, $this->translate('From')))->setDefaultVisible(),
      'content' => (new Text($this, $this->translate('Content')))->setRequired()->setDefaultVisible(),
      'thread_uid' => (new Text($this, $this->translate('Thread UID')))->setDefaultVisible(),
      'id_mail' => (new Lookup($this, $this->translate('Originating mail'), Mail::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add post');
    $description->show(['header', 'columnSearch', 'fulltextSearch']);
    $description->hide(['footer']);
    return $description;
  }

}
