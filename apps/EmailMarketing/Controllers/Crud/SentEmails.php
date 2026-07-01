<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Crud;

use Hubleto\App\Community\EmailMarketing\Models\CampaignScheduleRecipient;
use Hubleto\App\Community\EmailMarketing\Models\Recipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Description\Form;
use Hubleto\Framework\Description\Table;

class SentEmails extends \Hubleto\Framework\Controllers\CrudController
{

  public function describeTable(): Table
  {
    $description = new Table;
    $description->addColumn('subject', (new Varchar(null, 'Subject'))->setDefaultVisible());
    $description->addColumn('from', (new Varchar(null, 'From'))->setDefaultVisible());
    $description->addColumn('to', (new Varchar(null, 'To'))->setDefaultVisible());
    $description->addColumn('cc', (new Varchar(null, 'CC'))->setDefaultVisible());
    $description->addColumn('datetime_scheduled_to_send', (new Date(null, 'Scheduled to send'))->setDefaultVisible());
    $description->addColumn('datetime_sent', (new Date(null, 'Sent'))->setDefaultVisible());
    return $description;
  }

  public function describeForm(): Form
  {
    $description = new Form;
    return $description;
  }

  public function loadTableData(
    string $fulltextSearch = '',
    array $columnSearch = [],
    array $orderBy = [],
    int $itemsPerPage = 15,
    int $page = 0,
    string $dataView = ''
  ): array
  {
    /** @var Recipient */
    $mRecipientEmail = $this->getModel(Recipient::class);
    
    /** @var CampaignScheduleRecipient */
    $mRecipientCampaign = $this->getModel(CampaignScheduleRecipient::class);

    /** @var Mail */
    $mMail = $this->getModel(Mail::class);

    $query = $mMail->record->prepareReadQuery()
      ->leftJoin($mRecipientEmail->table, $mRecipientEmail->table . '.id_mail', '=', $mMail->table . '.id')
      ->leftJoin($mRecipientCampaign->table, $mRecipientCampaign->table . '.id_mail', '=', $mMail->table . '.id')
      ->select(
        $mMail->table . '.id',
        $mMail->table . '.subject',
        $mMail->table . '.from',
        $mMail->table . '.to',
        $mMail->table . '.cc',
        $mMail->table . '.datetime_scheduled_to_send',
        $mMail->table . '.datetime_sent',
      )
      ->whereNotNull($mRecipientEmail->table . '.id')
      ->orWhereNotNull($mRecipientCampaign->table . '.id')
      ->orderBy('datetime_sent', 'desc')
    ;

    $paginatedData = $query->paginate(
      $itemsPerPage,
      ['*'],
      'page',
      $page
    )->toArray();

    $paginatedData['records'] = $paginatedData['data'];
    unset($paginatedData['data']);

    return $paginatedData;
  }

}
