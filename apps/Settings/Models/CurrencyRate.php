<?php

namespace Hubleto\App\Community\Settings\Models;

use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Exceptions\RecordSaveException;

class CurrencyRate extends \Hubleto\Erp\Model
{
  public string $table = 'currency_rates';
  public string $recordManagerClass = RecordManagers\CurrencyRate::class;
  public ?string $lookupSqlValue = 'concat("Rate #", {%TABLE%}.id, " ", ifnull({%TABLE%}.valid_from, ""))';
  public ?string $lookupUrlAdd = 'settings/currency-rates/add';
  public ?string $lookupUrlDetail = 'settings/currency-rates/{%ID%}';

  public array $relations = [
    'CURRENCY' => [ self::BELONGS_TO, Currency::class, 'id_currency', 'id' ],
  ];

  /**
   * Describes columns of the model.
   *
   * @return array
   *
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnDelete('CASCADE')->setRequired()->setDefaultVisible(),
      'rate_to_default' => (new Decimal($this, $this->translate('Rate to default currency')))->setRequired()->setDefaultVisible()->setDecimals(8),
      'valid_from' => (new Date($this, $this->translate('Valid from')))->setRequired()->setDefaultVisible(),
      'source' => (new Varchar($this, $this->translate('Source')))->setDefaultVisible()->setDefaultValue('manual'),
    ]);
  }

  /**
   * Describes the table (list) of records.
   *
   * @return \Hubleto\Framework\Description\Table
   *
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add currency rate');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    return $description;
  }

  /**
   * Describes the form of a record.
   *
   * @return \Hubleto\Framework\Description\Form
   *
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    return $description;
  }

  /**
   * The rate must be positive.
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeValidate(array $record): array
  {
    $record = parent::onBeforeValidate($record);

    if ((float) ($record['rate_to_default'] ?? 0) <= 0) {
      throw new RecordSaveException($this->translate('The rate must be greater than zero.'), ['rate_to_default']);
    }

    return $record;
  }

}
