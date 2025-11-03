<?php

namespace Hubleto\App\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Boolean;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Template extends \Hubleto\Erp\Model
{
  public string $table = 'mails_templates';
  public string $recordManagerClass = RecordManagers\Template::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';
  public ?string $lookupUrlAdd = 'mail/templates/add';
  public ?string $lookupUrlDetail = 'mail/templates/{%ID%}';

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired()->setCssClass('font-bold')->setDefaultVisible(),
      'body_text' => (new Text($this, $this->translate('Body (Text)'))),
      'body_html' => (new Text($this, $this->translate('Body (HTML)')))->setReactComponent('InputTextareaWithHtmlPreview'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add template';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }}
