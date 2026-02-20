<?php

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\Framework\Helper;

class SendInvoiceInEmail extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $prepare = $this->router()->urlParamAsBool('prepare');
    $emailType = $this->router()->urlParamAsString('emailType');
    $idInvoice = $this->router()->urlParamAsInteger('idInvoice');

    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    /** @var Mail */
    $mMail = $this->getModel(Mail::class);

    $invoice = $mInvoice->record->prepareReadQuery()
      ->where('invoices.id', $idInvoice)
      ->with('PROFILE')
      ->with('ITEMS')
      ->with('PROFILE.SENDER_ACCOUNT')
      ->with('CUSTOMER.CONTACTS.VALUES')
      ->first()
    ;

    switch ($invoice->type) {
      case 1: $attachmentName = 'Proforma Invoice'; break;
      case 2: $attachmentName = 'Advance Invoice'; break;
      case 3: $attachmentName = 'Invoice'; break;
      case 4: $attachmentName = 'Credit Note'; break;
      case 5: $attachmentName = 'Debit Note'; break;
    }

    $attachmentName .=
      ' '
      . Helper::str2url($invoice->number)
      . ' '
      . Helper::str2url($invoice->CUSTOMER->name)
      . '.pdf'
    ;

    if ($prepare) {
      $subject = '';
      $bodyHtml = '';

      switch ($emailType) {
        case 'send-invoice':
          $subject = $invoice->PROFILE->mail_send_invoice_subject ?? '';
          $bodyHtml = $invoice->PROFILE->mail_send_invoice_body ?? '';
        break;
        case 'notify-due-invoice':
          $subject = $invoice->PROFILE->mail_send_due_warning_subject ?? '';
          $bodyHtml = $invoice->PROFILE->mail_send_due_warning_body ?? '';
        break;
      }

      $cc = $invoice->PROFILE->mail_send_invoice_cc ?? '';
      $bcc = $invoice->PROFILE->mail_send_invoice_bcc ?? '';

      $twigVars = $invoice->toArray();
      $twigVars['HUBLETO'] = [
        'locale' => $this->locale(),
      ];

      $twigTemplate = $this->renderer()->getTwig()->createTemplate($subject);
      $subject = $twigTemplate->render($twigVars);

      $twigTemplate = $this->renderer()->getTwig()->createTemplate($bodyHtml);
      $bodyHtml = $twigTemplate->render($twigVars);

      $recipients = [];
      if ($invoice->CUSTOMER->CONTACTS) {
        foreach ($invoice->CUSTOMER->CONTACTS as $contact) {
          if (!$contact->is_for_invoicing) continue;
          foreach ($contact->VALUES as $value) {
            if ($value->type == 'email') $recipients[] = $value['value'];
          }
        }
        
      }

      return [
        'senderAccount' => $invoice->PROFILE->SENDER_ACCOUNT,
        'subject' => $subject,
        'bodyHtml' => $bodyHtml,
        'to' => join(', ', $recipients),
        'cc' => $cc,
        'bcc' => $bcc,
        'attachments' => [
          [ 'name' => $attachmentName, 'file' => $invoice->pdf ]
        ]
      ];
    } else {
      $idSenderAccount = $this->router()->urlParamAsInteger('idSenderAccount');
      $subject = $this->router()->urlParamAsString('subject');
      $bodyHtml = $this->router()->urlParamAsString('bodyHtml');
      $to = $this->router()->urlParamAsString('to');
      $cc = $this->router()->urlParamAsString('cc');
      $bcc = $this->router()->urlParamAsString('bcc');

      if (empty($to)) throw new \Exception("Recipient must be provided.");

      try {

        $idMailSent = $mMail->createAndSend(
          [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'id_account' => $idSenderAccount,
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
          ],
          [
            [ 'name' => $attachmentName, 'file' => $invoice->pdf ]
          ]
        );

        if ($idMailSent > 0) {
          $mInvoice->record->where('invoices.id', $idInvoice)->update([
            'date_sent' => date('Y-m-d'),
          ]);
        }

        return ['status' => 'success'];
      } catch (\Throwable $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
      }

      return $result;
    }
  }
}
