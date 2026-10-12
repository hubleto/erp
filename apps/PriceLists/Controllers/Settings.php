<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

use Hubleto\App\Community\PriceLists\Loader as PriceListApp;
use Hubleto\App\Community\PriceLists\Services\PriceCalculator;
use Hubleto\App\Community\PriceLists\Services\Settings as SettingsService;

class Settings extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/Settings';

  /** Settings shown on the page: key => [type, label]. Types: string, int, float, bool, or a list of select options. */
  protected function getSettingsDefinition(): array
  {
    return [
      $this->translate('General') => [
        'numberingPattern' => ['string', $this->translate('Numbering pattern of price lists ({YYYY}, {YY}, {MM}, {DD}, {#})')],
      ],
      $this->translate('Calculation') => [
        'calc.rounding' => [['none' => $this->translate('None'), 'unit' => $this->translate('Per unit'), 'item' => $this->translate('Per item')], $this->translate('Rounding')],
        'calc.vatMode' => [['per_item' => $this->translate('VAT per item'), 'per_rate_group' => $this->translate('VAT per rate group')], $this->translate('VAT in totals')],
        'calc.decimals.unit' => ['int', $this->translate('Decimals of unit prices')],
        'calc.decimals.item' => ['int', $this->translate('Decimals of item and document amounts')],
        'calc.cashRounding' => ['float', $this->translate('Cash rounding step (0 = off)')],
        'vat.requireRate' => ['bool', $this->translate('Fail when the VAT rate cannot be determined')],
      ],
      $this->translate('Price resolution') => [
        'resolution.fallbackToProductPrice' => ['bool', $this->translate('Use the product price when no price list gives a price')],
        'applyMode' => [['off' => $this->translate('Off'), 'when_zero' => $this->translate('When the price is zero'), 'always' => $this->translate('Always')], $this->translate('Fill prices of saved document items automatically')],
        'applyToDocumentMode' => [['when_zero' => $this->translate('Only items with zero price'), 'always' => $this->translate('All items')], $this->translate('"Apply price list" button changes')],
        'deviationTolerancePercent' => ['float', $this->translate('Tolerated deviation of an item price from the price list (%)')],
        'blockBelowFloor' => ['bool', $this->translate('Refuse items with a price below the minimum of the price list')],
        'syncSupplierPrices' => ['bool', $this->translate('Copy purchase prices of suppliers to their purchase price lists')],
      ],
      $this->translate('Housekeeping') => [
        'expiryWarningDays' => ['int', $this->translate('Warn about expiring price lists (days before the end)')],
        'resolutionRetentionDays' => ['int', $this->translate('Keep resolutions for (days, 0 = forever)')],
        'maxBatchItems' => ['int', $this->translate('Maximum number of items in one batch request')],
      ],
    ];
  }

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var SettingsService $settings */
    $settings = $this->getService(SettingsService::class);
    $definition = $this->getSettingsDefinition();

    /** @var PriceListApp $app */
    $app = $this->appManager()->getApp('Hubleto\\App\\Community\\PriceLists');
    $consumers = $app->collectExtendibles('PriceCalculatorConsumers');

    if ($this->router()->urlParamAsBool('submitted')) {
      foreach ($definition as $group => $fields) {
        foreach ($fields as $key => [$type]) {
          $paramName = str_replace('.', '_', $key);
          if ($type === 'bool') $settings->save($key, $this->router()->urlParamAsBool($paramName));
          elseif ($type === 'int') $settings->save($key, $this->router()->urlParamAsInteger($paramName));
          elseif ($type === 'float') $settings->save($key, $this->router()->urlParamAsFloat($paramName));
          else $settings->save($key, $this->router()->urlParamAsString($paramName));
        }
      }

      // use PriceList as the price calculator of other apps (the apps read the config key priceCalculatorService)
      $enableAll = $this->router()->urlParamAsBool('enableAll');
      foreach ($consumers as $appClass => $consumer) {
        $enabled = $enableAll || $this->router()->urlParamAsBool('consumer_' . md5($appClass));
        $this->config()->save($consumer['configPath'], $enabled ? PriceCalculator::class : '');
      }
    }

    $values = [];
    foreach ($definition as $group => $fields) {
      foreach ($fields as $key => [$type, $label]) {
        $values[$key] = $type === 'bool' ? $settings->getBool($key) : $settings->getString($key);
      }
    }

    $consumerRows = [];
    foreach ($consumers as $appClass => $consumer) {
      $appNamespace = str_replace('\\Loader', '', $appClass);
      $consumerRows[] = [
        'uid' => md5($appClass),
        'title' => $consumer['title'],
        'installed' => $this->appManager()->isAppInstalled($appNamespace),
        'enabled' => $this->config()->getAsString($consumer['configPath'], '') === PriceCalculator::class,
      ];
    }

    $this->viewParams['definition'] = $definition;
    $this->viewParams['values'] = $values;
    $this->viewParams['consumers'] = $consumerRows;
    $this->viewParams['submitted'] = $this->router()->urlParamAsBool('submitted');

    $this->setView('@Hubleto:App:Community:PriceLists/Settings.twig');
  }
}
