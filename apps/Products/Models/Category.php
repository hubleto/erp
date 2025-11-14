<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Image;

class Category extends \Hubleto\Erp\Model
{
  public string $table = 'product_categories';
  public string $recordManagerClass = RecordManagers\Category::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'products/categories/{%ID%}';
  public ?string $lookupUrlAdd = 'products/categories/add';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_parent' => (new Lookup($this, $this->translate("Parent"), Category::class))->setDefaultVisible(),
      'name' => (new Varchar($this, $this->translate("Name")))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'color' => (new Color($this, $this->translate("Color")))->setDefaultVisible()->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'short_description' => (new Text($this, $this->translate("Short description"))),
      'long_description' => (new Text($this, $this->translate("Long description")))->setReactComponent('InputWysiwyg'),
      'photo_1' => (new Image($this, $this->translate("Photo #1")))->setDefaultVisible(),
      'photo_2' => (new Image($this, $this->translate("Photo #2"))),
      'photo_3' => (new Image($this, $this->translate("Photo #3"))),
      'photo_4' => (new Image($this, $this->translate("Photo #4"))),
      'photo_5' => (new Image($this, $this->translate("Photo #5"))),
      'url_slug' => (new Varchar($this, $this->translate("URL slug")))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui["addButtonText"] = $this->translate("Add product category");
    $description->ui["dataView"] = 'tree';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}
