<?php

namespace HubletoApp\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;

use HubletoApp\Community\Crypto\KeyManager;

class Account extends \HubletoMain\Model
{

  public string $table = 'mails_accounts';
  public string $recordManagerClass = RecordManagers\Account::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setCssClass('font-bold'),
      'color' => (new Color($this, $this->translate('Color'))),
      'smtp_host' => (new Varchar($this, $this->translate('SMTP host')))->setRequired(),
      'smtp_port' => (new Integer($this, $this->translate('SMTP port')))->setRequired(),
      'smtp_encryption' => (new Varchar($this, $this->translate('SMTP encryption')))->setRequired(),
      'smtp_username' => (new Varchar($this, $this->translate('SMTP username')))->setRequired(),
      'smtp_password' => (new Password($this, $this->translate('SMTP password')))->setRequired(),
    ]);
  }

  public function encryptPassword(string $original): string
  {
    return KeyManager::encryptString($original);
  }

  public function decryptPassword(string $encrypted): string
  {
    return KeyManager::decryptString($encrypted);
  }

}
