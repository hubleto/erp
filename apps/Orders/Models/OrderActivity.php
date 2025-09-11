<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Contacts\Models\Contact;

class OrderActivity extends \Hubleto\App\Community\Calendar\Models\Activity
{
  public string $table = 'order_activities';
  public string $recordManagerClass = RecordManagers\OrderActivity::class;

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
    ]);
  }

}
