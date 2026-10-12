<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\Mail\Loader as MailApp;
use Hubleto\App\Community\PriceLists\Models\PriceList;

/**
 * Generates the PDF of a price list and sends a link to it by e-mail with Mail\Loader->send().
 * Parameters: idPriceList, to (e-mail address), subject, body.
 */
class SendByEmail extends GeneratePdf
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');
    $to = trim($this->router()->urlParamAsString('to'));

    if ($to === '') return ['status' => 'error', 'message' => $this->translate('Enter the e-mail address of the recipient.')];

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $priceList = $mPriceList->record->where($mPriceList->table . '.id', $idPriceList)->first();
    if (!$priceList) return ['status' => 'error', 'message' => $this->translate('Price list was not found.')];

    try {
      $pdf = $this->generate($priceList);

      /** @var MailApp $mail */
      $mail = $this->getService(MailApp::class);

      $subject = $this->router()->urlParamAsString('subject', $this->translate('Price list') . ' ' . $priceList->code . ' ' . $priceList->name);
      $body = $this->router()->urlParamAsString('body', $this->translate('Please find the price list at the link below.'))
        . '<br/><br/><a href="' . $this->env()->uploadUrl . '/' . $pdf['file'] . '">' . $pdf['file'] . '</a>'
      ;

      $mail->send($to, '', '', $subject, $body);
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return ['status' => 'success', 'sent' => true];
  }
}
