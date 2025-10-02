<?php

namespace Hubleto\App\Community\Cashdesk;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^cashdesk\/?$/' => Controllers\Home::class,

      '/^cashdesk\/cash-registers(\/(?<recordId>\d+))?\/?$/' => Controllers\CashRegisters::class,
      '/^cashdesk\/cash-registers\/add\/?$/' => ['controller' => Controllers\CashRegisters::class, 'vars' => ['recordId' => -1]],

      '/^cashdesk\/receipts(\/(?<recordId>\d+))?\/?$/' => Controllers\Receipts::class,
      '/^cashdesk\/receipts\/add\/?$/' => ['controller' => Controllers\Receipts::class, 'vars' => ['recordId' => -1]],
    ]);

  }

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\CashRegister::class)->dropTableIfExists()->install();
      $this->getModel(Models\Receipt::class)->dropTableIfExists()->install();
      $this->getModel(Models\ReceiptItem::class)->dropTableIfExists()->install();
    }
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/cashdesk/cash-registers">
          <span class="icon"><i class="fas fa-cash-register"></i></span>
          <span class="text">' . $this->translate('Cash registers') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/cashdesk/receipts">
          <span class="icon"><i class="fas fa-receipt"></i></span>
          <span class="text">' . $this->translate('Receipts') . '</span>
        </a>
      </div>
    ';
  }

  /**
   * [Description for generateDemoData]
   *
   * @return void
   * 
   */
  public function generateDemoData(): void
  {
    $mCashRegister = $this->getModel(Models\CashRegister::class);
    $mReceipt = $this->getModel(Models\Receipt::class);
    $mReceiptItem = $this->getModel(Models\ReceiptItem::class);

    $idCashRegister = $mCashRegister->record->recordCreate([
      'id_company' => 1,
      'identifier' => rand(1000, 9999) . rand(1000, 9999),
      'description' => 'DEMO Cash Register'
    ])['id'];

    $mProduct = $this->getModel(\Hubleto\App\Community\Products\Models\Product::class);
    $idsProduct = $mProduct->record->pluck('id');

    for ($i = 1; $i < 10; $i++) {
      $idReceipt = $mReceipt->record->recordCreate([
        'id_company' => 1,
        'id_cash_register' => $idCashRegister,
        'number' => date('Y') . '-' . $i,
        'created' => date('Y-m-d H:i:s'),
      ])['id'];

      $totalReceiptPriceExclVat = 0;
      $totalReceiptPriceInclVat = 0;

      for ($j = 1; $j < rand(3, 5); $j++) {
        $vatPercent = 23;
        $quantity = rand(1, 25);

        $unitPriceExclVat = rand(100, 200) / 23;
        $unitVat = $unitPriceExclVat * $vatPercent / 100;
        $unitPriceInclVat = $unitPriceExclVat + $unitVat;
        $totalPriceExclVat = $unitPriceExclVat * $quantity;
        $totalVat = $unitVat * $quantity;
        $totalPriceInclVat = $unitPriceExclVat * $quantity;

        $totalReceiptPriceExclVat += $totalPriceExclVat;
        $totalReceiptPriceInclVat += $totalPriceInclVat;

        $mReceiptItem->record->recordCreate([
          'id_receipt' => $idReceipt,
          'id_product' => $idsProduct[rand(0, count($idsProduct) - 1)],
          'quantity' => $quantity,
          'vat_percent' => $vatPercent,
          'unit_price_excl_vat' => $unitPriceExclVat,
          'unit_price_incl_vat' => $unitPriceInclVat,
          'unit_vat' => $unitVat,
          'total_price_excl_vat' => $totalPriceExclVat,
          'total_price_incl_vat' => $totalPriceInclVat,
          'total_vat' => $totalVat,
        ]);
      }

      $mReceipt->record->where('id', $idReceipt)->update([
        'total_price_excl_vat' => $totalReceiptPriceExclVat,
        'total_price_incl_vat' => $totalReceiptPriceInclVat,
      ]);
    }
  }

}
