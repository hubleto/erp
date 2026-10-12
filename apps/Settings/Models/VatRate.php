<?php

namespace Hubleto\App\Community\Settings\Models;

use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Exceptions\RecordSaveException;

class VatRate extends \Hubleto\Erp\Model
{
  public const TYPE_STANDARD = 1;
  public const TYPE_REDUCED = 2;
  public const TYPE_SUPER_REDUCED = 3;
  public const TYPE_ZERO = 4;
  public const TYPE_EXEMPT = 5;
  public const TYPE_REVERSE_CHARGE = 6;

  public string $table = 'vat_rates';
  public string $recordManagerClass = RecordManagers\VatRate::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.code, ""), " ", ifnull({%TABLE%}.percent, 0), "%")';
  public ?string $lookupUrlAdd = 'settings/vat-rates/add';
  public ?string $lookupUrlDetail = 'settings/vat-rates/{%ID%}';

  public array $relations = [
    'COUNTRY' => [ self::BELONGS_TO, Country::class, 'id_country', 'id' ],
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
      'code' => (new Varchar($this, $this->translate('Code')))->setRequired()->setDefaultVisible()->setCssClass('badge badge-info'),
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setCssClass('font-bold'),
      'percent' => (new Decimal($this, $this->translate('Percent')))->setRequired()->setDefaultVisible()->setDecimals(2)->setUnit('%'),
      'type' => (new Integer($this, $this->translate('Type')))->setDefaultVisible()->setDefaultValue(self::TYPE_STANDARD)->setEnumValues([self::TYPE_STANDARD => $this->translate('Standard'), self::TYPE_REDUCED => $this->translate('Reduced'), self::TYPE_SUPER_REDUCED => $this->translate('Super reduced'), self::TYPE_ZERO => $this->translate('Zero'), self::TYPE_EXEMPT => $this->translate('Exempt'), self::TYPE_REVERSE_CHARGE => $this->translate('Reverse charge')]),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class))->setFkOnDelete('SET NULL')->setDefaultVisible(),
      'valid_from' => (new Date($this, $this->translate('Valid from')))->setDefaultVisible(),
      'valid_to' => (new Date($this, $this->translate('Valid to')))->setDefaultVisible(),
      'is_default' => (new Boolean($this, $this->translate('Is default')))->setDefaultVisible(),
      'note' => (new Text($this, $this->translate('Note'))),
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
    $description->ui['addButtonText'] = $this->translate('Add VAT rate');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fValidNow', [
      'title' => $this->translate('Validity'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Valid now'), 2 => $this->translate('Not valid now') ],
    ]);

    $description->addFilter('fType', [
      'title' => $this->translate('Type'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Standard'), 2 => $this->translate('Reduced'), 3 => $this->translate('Super reduced'), 4 => $this->translate('Zero'), 5 => $this->translate('Exempt'), 6 => $this->translate('Reverse charge') ],
    ]);

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
   * Validates the percentage and that rates with the same code do not overlap in time.
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeValidate(array $record): array
  {
    $record = parent::onBeforeValidate($record);

    $percent = (float) ($record['percent'] ?? 0);
    if ($percent < 0 || $percent > 100) {
      throw new RecordSaveException($this->translate('VAT percentage must be between 0 and 100.'), ['percent']);
    }

    $id = (int) ($record['id'] ?? 0);
    $from = (string) ($record['valid_from'] ?? '');
    $to = (string) ($record['valid_to'] ?? '');
    if ($from !== '' && $to !== '' && $from > $to) {
      throw new RecordSaveException($this->translate('"Valid from" must not be later than "Valid to".'), ['valid_from', 'valid_to']);
    }

    $query = $this->record->where($this->table . '.code', (string) ($record['code'] ?? ''));
    $idCountry = (int) ($record['id_country'] ?? 0);
    $query = $idCountry > 0 ? $query->where($this->table . '.id_country', $idCountry) : $query->whereNull($this->table . '.id_country');
    if ($id > 0) $query = $query->where($this->table . '.id', '!=', $id);

    $newFrom = $from !== '' ? $from : '0000-01-01';
    $newTo = $to !== '' ? $to : '9999-12-31';
    foreach ($query->get() as $other) {
      $otherFrom = !empty($other->valid_from) ? (string) $other->valid_from : '0000-01-01';
      $otherTo = !empty($other->valid_to) ? (string) $other->valid_to : '9999-12-31';
      if ($otherFrom <= $newTo && $otherTo >= $newFrom) {
        throw new RecordSaveException(
          $this->translate('A VAT rate with the same code and country already exists in the given period.'),
          ['code', 'valid_from', 'valid_to']
        );
      }
    }

    return $record;
  }

  /**
   * Keeps one default VAT rate per country.
   *
   * @param array $savedRecord
   *
   * @return array
   *
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    $this->enforceSingleDefault($savedRecord);
    return $savedRecord;
  }

  /**
   * Keeps one default VAT rate per country.
   *
   * @param array $originalRecord
   * @param array $savedRecord
   *
   * @return array
   *
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);
    $this->enforceSingleDefault($savedRecord);
    return $savedRecord;
  }

  /**
   * Unsets the default flag of other rates of the same country.
   *
   * @param array $record
   *
   * @return void
   *
   */
  protected function enforceSingleDefault(array $record): void
  {
    if (empty($record['is_default']) || empty($record['id'])) return;

    $query = $this->record->where($this->table . '.id', '!=', (int) $record['id'])->where($this->table . '.is_default', 1);
    $idCountry = (int) ($record['id_country'] ?? 0);
    $query = $idCountry > 0 ? $query->where($this->table . '.id_country', $idCountry) : $query->whereNull($this->table . '.id_country');
    $query->update(['is_default' => 0]);
  }

}
