<?php

namespace Hubleto\App\Community\Shops\Models;


use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Image;

class Shop extends \Hubleto\Erp\Model
{
  public string $table = 'shops';
  public string $recordManagerClass = RecordManagers\Shop::class;
  public ?string $lookupSqlValue = '{%TABLE%}.address';
  public ?string $lookupUrlDetail = 'shops/{%ID%}';

  public array $relations = [
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate("Name")))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'color' => (new Color($this, $this->translate("Color")))->setDefaultVisible()->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'address' => (new Varchar($this, $this->translate('Address')))->setDefaultVisible()->setIcon(self::COLUMN_ADDRESS_DEFAULT_ICON),
      'short_description' => (new Text($this, $this->translate("Short description"))),
      'long_description' => (new Text($this, $this->translate("Long description")))->setReactComponent('InputWysiwyg'),
      'photo_1' => (new Image($this, $this->translate("Photo #1")))->setDefaultVisible(),
      'photo_2' => (new Image($this, $this->translate("Photo #2"))),
      'photo_3' => (new Image($this, $this->translate("Photo #3"))),
      'photo_4' => (new Image($this, $this->translate("Photo #4"))),
      'photo_5' => (new Image($this, $this->translate("Photo #5"))),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add shop';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

}
