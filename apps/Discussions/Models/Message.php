<?php

namespace Hubleto\App\Community\Discussions\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Auth\Models\User;


class Message extends \Hubleto\Erp\Model
{
  public string $table = 'discussions_messages';
  public string $recordManagerClass = RecordManagers\Message::class;
  public ?string $lookupSqlValue = 'concat("Message #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'messages/{%ID%}';

  public array $relations = [
    'DISCUSSION' => [ self::BELONGS_TO, Discussion::class, 'id_discussion', 'id' ],
    'FROM' => [ self::BELONGS_TO, User::class, 'id_from', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_discussion' => (new Lookup($this, $this->translate('Discussion'), Discussion::class))->setDefaultVisible()->setRequired(),
      'id_from' => (new Lookup($this, $this->translate('From'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'from_email' => (new Varchar($this, $this->translate('From (Email)')))->setDefaultVisible(),
      'message' => (new Text($this, $this->translate('Text')))->setDefaultVisible(),
      'sent' => (new DateTime($this, $this->translate('Sent')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Message';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    // Uncomment and modify these lines if you want to define table filter for your model
    // $description->ui['filters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

    return $description;
  }

}
