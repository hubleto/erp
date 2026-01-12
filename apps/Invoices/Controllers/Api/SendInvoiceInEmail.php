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

    if ($prepare) {
      $subject = $invoice->PROFILE->mail_send_invoice_subject ?? '';
      $bodyHtml = $invoice->PROFILE->mail_send_invoice_body ?? '';
      $cc = $invoice->PROFILE->mail_send_invoice_cc ?? '';
      $bcc = $invoice->PROFILE->mail_send_invoice_bcc ?? '';

      $twigTemplate = $this->renderer()->getTwig()->createTemplate($subject);
      $subject = $twigTemplate->render($invoice->toArray());

      $twigTemplate = $this->renderer()->getTwig()->createTemplate($bodyHtml);
      $bodyHtml = $twigTemplate->render($invoice->toArray());

      $recipients = [];
      if ($invoice->CUSTOMER->CONTACTS) {
        foreach ($invoice->CUSTOMER->CONTACTS as $contact) {
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
        'bcc' => $bcc
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
            [
              'name' =>
                'Invoice '
                . Helper::str2url($invoice->number)
                . ' '
                . Helper::str2url($invoice->CUSTOMER->name)
                . '.pdf',
              'file' => $invoice->pdf,
            ]
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
