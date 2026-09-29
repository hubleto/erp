<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Candidate extends \Hubleto\Erp\Model
{
  public string $table = 'hr_candidates';
  public string $recordManagerClass = RecordManagers\Candidate::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.first_name, " ", {%TABLE%}.last_name)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setDefaultVisible()->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setDefaultVisible()->setRequired(),
      'email' => (new Varchar($this, $this->translate('Email')))->setDefaultVisible()->setRequired(),
      'phone' => (new Varchar($this, $this->translate('Phone'))),
      'source' => (new Varchar($this, $this->translate('Source')))->setDefaultVisible(),
      'portfolio_url' => (new Varchar($this, $this->translate('Portfolio or profile URL'))),
      'consent_to_store_data' => (new Boolean($this, $this->translate('Consent to store application data')))->setRequired(),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Candidates');
    $description->ui['addButtonText'] = $this->translate('Add candidate');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
  }
}