<?php

namespace Hubleto\App\Community\HrPerformance\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Course extends \Hubleto\Erp\Model
{
  public string $table = 'hr_courses';
  public string $recordManagerClass = RecordManagers\Course::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Course name')))->setDefaultVisible()->setRequired(),
      'provider' => (new Varchar($this, $this->translate('Provider')))->setDefaultVisible(),
      'delivery' => (new Varchar($this, $this->translate('Delivery method'))),
      'duration_hours' => (new Integer($this, $this->translate('Duration (hours)'))),
      'url' => (new Varchar($this, $this->translate('Course URL')))->setReactComponent('InputHyperlink'),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultVisible()->setDefaultValue(1),
      'description' => (new Text($this, $this->translate('Description'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add course');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
  }
}