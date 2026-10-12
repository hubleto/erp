# PriceList (Hubleto custom app)

Price lists, quantity breaks, customer-specific prices, promotions and central price and VAT arithmetic.

Namespace: `Hubleto\App\Community\PriceLists`. URL slug: `price-lists`.

## Install

1. Unpack this folder to `src/apps/PriceList` of your custom Hubleto project.
2. Run `php hubleto app install PriceList` (apps listed in `manifest.yaml` under `requires` are installed first).
3. Open `price-lists`. Settings are under `settings/price-lists`.
4. Optional: on the Settings page switch on "Use PriceList as the price calculator of other apps" (Orders, Deals, Invoices). This only sets the config key `priceCalculatorService` of those apps. Results are identical with the default settings.

## Contents

- `Models/` price lists (`PriceList`), prices (`Item`), assignments, rules, currency rates, VAT rates, history, resolutions, document links.
- `Services/` all price arithmetic (`PriceCalculator` implements `Hubleto\Erp\Interfaces\PriceCalculatorInterface`), VAT, rounding, currency conversion and the price resolver.
- `Controllers/Api` price resolution, calculation, document pricing, copy, bulk adjust, PDF, e-mail.
- `Extendibles/PriceListItemAdapters`, `Extendibles/PriceCalculatorConsumers` extendibles other apps can implement (examples in `community-apps-patches.md`).
- `Loader.tsx` registers components and injects tabs and an "Apply price list" button into forms of other apps with `FormCustomizer`.
- `community-apps-patches.md`, `community-core-patches.md`, `hubleto-framework-patches.md` changes and suggestions for other parts of Hubleto.

## Tests

`Tests/` contains PHPUnit tests (`Hubleto\Erp\TestCase`). `Tests/Data/price-vectors.json` holds the arithmetic vectors shared by the calculator tests.
