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

class Mail extends \Hubleto\Erp\Model
{
  public string $table = 'mails';
  public string $recordManagerClass = RecordManagers\Mail::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';
  // public string $lookupUrlDetail = 'mail/{%ID%}';

  public array $relations = [
    'ACCOUNT' => [ self::BELONGS_TO, Account::class, 'id_account', 'id' ],
    'MAILBOX' => [ self::BELONGS_TO, Mailbox::class, 'id_mailbox', 'id' ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    $user = $this->authProvider()->getUser();
    return array_merge(parent::describeColumns(), [
      'mail_number' => (new Varchar($this, $this->translate('Mail Id')))->addIndex('INDEX `mail_number` (`mail_number`)'),
      'mail_id' => (new Varchar($this, $this->translate('Mail Number')))->addIndex('INDEX `mail_id` (`mail_id`)'),
      'id_account' => (new Lookup($this, $this->translate('Account'), Account::class))->setReadonly(),
      'id_mailbox' => (new Lookup($this, $this->translate('Mailbox'), Mailbox::class))->setReadonly(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setRequired()->setDefaultValue(1),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'datetime_scheduled_to_send' => (new DateTime($this, $this->translate('Scheduled to send'))),
      'datetime_sent' => (new DateTime($this, $this->translate('Sent')))->setReadonly(),
      'datetime_read' => (new DateTime($this, $this->translate('Read'))),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setRequired()->setCssClass('font-bold'),
      'from' => (new Varchar($this, $this->translate('From')))->setReadonly()->setDefaultValue($user['email'] ?? ''),
      'to' => (new Varchar($this, $this->translate('To'))),
      'cc' => (new Varchar($this, $this->translate('Cc'))),
      'bcc' => (new Varchar($this, $this->translate('Bcc'))),
      'body_text' => (new Text($this, $this->translate('Body (Text)'))),
      'body_html' => (new Text($this, $this->translate('Body (HTML)')))->setReactComponent('InputWysiwyg'),
      'color' => (new Color($this, $this->translate('Color'))),
      'is_draft' => (new Boolean($this, $this->translate('Draft')))->setDefaultValue(true),
      'is_template' => (new Boolean($this, $this->translate('Template'))),
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
    $folder = $this->router()->urlParamAsString('folder');

    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'New message';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['body']);
    unset($description->columns['color']);
    unset($description->columns['priority']);
    unset($description->columns['read']);

    switch ($folder) {
      case 'inbox':
        unset($description->columns['to']);
        unset($description->columns['cc']);
        unset($description->columns['bcc']);
        break;
      case 'sent':
        unset($description->columns['from']);
        break;
    }

    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;

    return $description;
  }

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->permissions['canDelete'] = false;
    $description->permissions['canUpdate'] = false;
    $description->ui['addButtonText'] = 'Save draft';
    $description->ui['saveButtonText'] = 'Save draft';

    return $description;
  }

  public function validateBeforeSending(array $mail): void
  {
    if (!class_exists(PHPMailer::class)) {
      throw new \Exception('PHPMailer is required to send emails. Run `composer require phpmailer/phpmailer` to install it.');
    }

    if ((int) $mail['id'] <= 0) throw new \Exception('Email ID is missing.');
    if (!empty($mail['datetime_sent'])) throw new \Exception('Email has already been sent.');
    if (!empty($mail['datetime_scheduled_to_send']) && $mail['datetime_scheduled_to_send'] > date('Y-m-d H:i:s')) throw new \Exception('Email is scheduled to be sent later.');
    if (empty($mail['subject'])) throw new \Exception('Email has not subject.');
    if (!filter_var($mail['to'], FILTER_VALIDATE_EMAIL)) throw new \Exception('Email does not have a valid recipient address.');
    if (empty($mail['ACCOUNT'])) throw new \Exception('Email has no account information.');
    if (
      empty($mail['ACCOUNT']['smtp_host'])
      || empty($mail['ACCOUNT']['smtp_port'])
      || empty($mail['ACCOUNT']['smtp_encryption'])
      || empty($mail['ACCOUNT']['smtp_username'])
      || empty($mail['ACCOUNT']['smtp_password'])
    ) {
      throw new \Exception('Email\'s account SMTP is not properly configured.');
    }
    if (empty($mail['ACCOUNT']['sender_email'])) {
      throw new \Exception('Email\'s account sender is not properly configured.');
    }
  }

  /**
   * [Description for send]
   *
   * @param array $mail
   * 
   * @return bool
   * 
   */
  public function send(array $mail): bool
  {

    $this->validateBeforeSending($mail);

    $mailer = new PHPMailer(true);
    try {
      /** @var Account */
      $mAccount = $this->getModel(Account::class);
      $password = $mAccount->decryptPassword($mail['ACCOUNT']['smtp_password']);

      $mailer->isSMTP();
      $mailer->SMTPAuth = true;
      $mailer->CharSet = "UTF-8";
      $mailer->Host = $mail['ACCOUNT']['smtp_host'];
      $mailer->Port = $mail['ACCOUNT']['smtp_port'];
      $mailer->SMTPSecure = $mail['ACCOUNT']['smtp_encryption'];
      $mailer->Username = $mail['ACCOUNT']['smtp_username'];
      $mailer->Password = $password;

      $mailer->setFrom(
        $mail['ACCOUNT']['sender_email'],
        $mail['ACCOUNT']['sender_name'] ?? ''
      );

      $mailer->addAddress($mail['to']);

      $mailer->isHTML(true);
      $mailer->Subject = $mail['subject'];
      $mailer->Body = $mail['body_html'];

      $this->logger()->info('Sending email to `' . $mail['to'] . '` from `' . $mail['ACCOUNT']['smtp_username'] . '`');
      $this->logger()->debug(print_r($mail['ACCOUNT'], true));
      $this->logger()->debug($password);

      $sent = $mailer->send();

      if ($sent) {
        $this->record
          ->where('id', $mail['id'])
          ->update([
            'datetime_sent' => date('Y-m-d H:i:s'),
          ])
        ;
      }

      return $sent;
    } catch (\Throwable $e) {
      throw new \Exception("Mailer Error: " . $e->getMessage());
    }
  }

  /**
   * [Description for sendById]
   *
   * @param int $id
   * 
   * @return bool
   * 
   */
  public function sendById(int $id): bool
  {
    $mail = $this->record->prepareReadQuery()->where('mails.id', $id)->first()?->toArray();
    return $this->send($mail);
  }

  public function createAndSend(array $mailData): bool
  {
    $mail = $this->record->recordCreate($mailData);
    return $this->sendById((int) $mail['id']);
  }

}
