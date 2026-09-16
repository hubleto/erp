<?php declare(strict_types=1);

namespace Hubleto\App\Community\Invoices\Controllers\Api;

use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Profile;

class GenerateInvoiceNumber extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    $idInvoice = $this->router()->urlParamAsInteger('idInvoice', 0);

    /** @var Invoice */
    $mInvoice = $this->getService(Invoice::class);
    $invoice = $mInvoice->record->where('id', $idInvoice)->first();
    if (!$invoice) throw new \Exception('Invoice not found.');
    $idProfile = $invoice['id_profile'] ?? 0;

    /** @var Profile */
    $mProfile = $this->getService(Profile::class);
    $profile = $mProfile->record->where('id', $idProfile)->first();
    if (!$profile) throw new \Exception('Invoice profile not found.');

    $numberingPattern = (string) ($profile->numbering_pattern ?? 'YYYY/NNNN');

    $invoicesThisYear = $mInvoice->record
      ->whereYear('date_delivery', date('Y'))
      ->where('id', '!=', $idInvoice)
      ->where('id_profile', $idProfile)
      ->where('inbound_outbound', $invoice->inbound_outbound)
      ->where('type', $invoice->type)
      ->get()
    ;

    $dueDays = $profile['due_days'] ?? 14;
    if ($dueDays < 0) $dueDays = 0;

    // Extract start and end offset of the invoice number from the
    // numbering pattern of the invoicing profile
    $nPositionStart = -1;
    $nPositionEnd = -1;
    for ($n = 0; $n < strlen($numberingPattern); $n++) {
      $c = $numberingPattern[$n];
      if ($c == 'N' && $nPositionStart == -1) {
        $nPositionStart = $n;
      } else if ($c != 'N' && $nPositionStart >= 0) {
        $nPositionEnd = $n;
      }
    }
    if ($nPositionEnd == -1) $nPositionEnd = $n;

    // Extract highest number of the invoice
    $maxNumber = 0;
    foreach ($invoicesThisYear as $i) {
      $maxNumber = max($maxNumber, (int) substr($i->number ?? '', $nPositionStart, $nPositionEnd));
    }

    $invoiceTypePrefixes = @json_decode($profile['invoice_type_prefixes'], true);
    $recordTypeAsString = Invoice::TYPES[$invoice->type] ?? '';

    // Calculate number of the new invoice
    $number = $numberingPattern;
    $number = str_replace('T', $invoiceTypePrefixes[$recordTypeAsString] ?? '', $number);
    $number = str_replace('YYYY', date('Y'), $number);
    $number = str_replace('YY', date('y'), $number);
    $number = str_replace('MM', date('m'), $number);
    $number = str_replace('DD', date('d'), $number);
    $number = str_replace('NNNNNN', str_pad((string) ($maxNumber + 1), 6, '0', STR_PAD_LEFT), $number);
    $number = str_replace('NNNNN', str_pad((string) ($maxNumber + 1), 5, '0', STR_PAD_LEFT), $number);
    $number = str_replace('NNNN', str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT), $number);
    $number = str_replace('NNN', str_pad((string) ($maxNumber + 1), 3, '0', STR_PAD_LEFT), $number);
    $number = str_replace('NN', str_pad((string) ($maxNumber + 1), 2, '0', STR_PAD_LEFT), $number);

    $mInvoice->record->where('id', $idInvoice)->update(['number' => $number]);

    return [
      'status' => 'success',
      'idInvoice' => $idInvoice,
      'number' => $number,
    ];
  }
}