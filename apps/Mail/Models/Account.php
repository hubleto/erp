<?php

namespace Hubleto\App\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;

use Hubleto\App\Community\Crypto\KeyManager;

class Account extends \Hubleto\Erp\Model
{

  public string $table = 'mails_accounts';
  public string $recordManagerClass = RecordManagers\Account::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MAILBOXES' => [ self::HAS_MANY, Mailbox::class, 'id_account', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'color' => (new Color($this, $this->translate('Color')))->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'sender_email' => (new Varchar($this, $this->translate('Sender email address'))),
      'sender_name' => (new Varchar($this, $this->translate('Sender name'))),
      'imap_host' => (new Varchar($this, $this->translate('IMAP host'))),
      'imap_port' => (new Integer($this, $this->translate('IMAP port'))),
      'imap_encryption' => (new Varchar($this, $this->translate('IMAP encryption')))->setEnumValues(['ssl' => 'ssl', 'tls' => 'tls']),
      'imap_username' => (new Varchar($this, $this->translate('IMAP username'))),
      'imap_password' => (new Password($this, $this->translate('IMAP password'))),
      'smtp_host' => (new Varchar($this, $this->translate('SMTP host'))),
      'smtp_port' => (new Integer($this, $this->translate('SMTP port'))),
      'smtp_encryption' => (new Varchar($this, $this->translate('SMTP encryption')))->setEnumValues(['ssl' => 'ssl', 'tls' => 'tls']),
      'smtp_username' => (new Varchar($this, $this->translate('SMTP username'))),
      'smtp_password' => (new Password($this, $this->translate('SMTP password'))),
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

    $description->ui['addButtonText'] = 'Add account';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
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
