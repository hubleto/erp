<?php

namespace Hubleto\App\Community\Mail\Models;


use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Attachment extends \Hubleto\Erp\Model
{
  public string $table = 'mails_attachments';
  public string $recordManagerClass = RecordManagers\Attachment::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MAIL' => [ self::BELONGS_TO, Mail::class, 'id_mail', 'id' ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    $user = $this->getService(\Hubleto\Framework\AuthProvider::class)->getUser();
    return array_merge(parent::describeColumns(), [
      'id_mail' => (new Lookup($this, $this->translate('Mail'), Mail::class))->setReadonly(),
      'name' => (new Varchar($this, $this->translate('Name')))->setDefaultVisible(),
      'file' => (new File($this, $this->translate('File')))->setDefaultVisible(),
    ]);
  }

}
