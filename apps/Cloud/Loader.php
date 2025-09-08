<?php

namespace Hubleto\App\Community\Cloud;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public bool $isPremium = false;

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $premiumAccount = $this->getService(PremiumAccount::class);

    $this->isPremium = $premiumAccount->premiumAccountActivated();

    $this->router()->get([
      '/^cloud\/?$/' => Controllers\Dashboard::class,
      '/^cloud\/api\/accept-legal-documents\/?$/' => Controllers\Api\AcceptLegalDocuments::class,
      '/^cloud\/api\/activate-premium-account\/?$/' => Controllers\Api\ActivatePremiumAccount::class,
      '/^cloud\/api\/charge-credit\/?$/' => Controllers\Api\ChargeCredit::class,
      '/^cloud\/api\/pay-monthly\/?$/' => Controllers\Api\PayMonthly::class,
      '/^cloud\/log\/?$/' => Controllers\Log::class,
      '/^cloud\/test\/make-random-payment\/?$/' => Controllers\Test\MakeRandomPayment::class,
      '/^cloud\/test\/clear-credit\/?$/' => Controllers\Test\ClearCredit::class,
      '/^cloud\/activate-subscription-renewal\/?$/' => Controllers\ActivateSubscriptionRenewal::class,
      '/^cloud\/deactivate-subscription-renewal\/?$/' => Controllers\DeactivateSubscriptionRenewal::class,
      '/^cloud\/payments-and-invoices\/?$/' => Controllers\PaymentsAndInvoices::class,
      '/^cloud\/billing-accounts\/?$/' => Controllers\BillingAccounts::class,
      '/^cloud\/make-payment\/?$/' => Controllers\MakePayment::class,
    ]);

    $premiumAccount->updatePremiumInfo();

  }

  public function onBeforeRender(): void
  {
    if ($this->authProvider()->getUserId() > 0) {
      if (!str_starts_with($this->router()->getRoute(), 'cloud')) {
        if (!$this->config()->getAsBool('legalDocumentsAccepted')) {
          $this->router()->redirectTo('cloud');
        } elseif ($this->isPremium) {
          $premiumAccount = $this->getService(PremiumAccount::class);
          $subscriptionInfo = $premiumAccount->getSubscriptionInfo();
          if (!$subscriptionInfo['isActive']) {
            $this->router()->redirectTo('cloud');
          }
        }
      }
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\BillingAccount::class)->dropTableIfExists()->install();
      $this->getModel(Models\Log::class)->dropTableIfExists()->install();
      $this->getModel(Models\Credit::class)->dropTableIfExists()->install();
      $this->getModel(Models\Payment::class)->dropTableIfExists()->install();
      $this->getModel(Models\Discount::class)->dropTableIfExists()->install();
    }
  }

  public function dangerouslyInjectDesktopHtmlContent(string $where): string
  {
    $premiumAccount = $this->getService(PremiumAccount::class);

    $freeTrialInfo = $premiumAccount->getFreeTrialInfo();
    $isTrialPeriod = $freeTrialInfo['isTrialPeriod'];
    $trialPeriodExpiresIn = $freeTrialInfo['trialPeriodExpiresIn'];

    if ($where == 'beforeSidebar') {
      if ($isTrialPeriod) {
        return '
          <a class="btn btn-square bg-red-50 text-red-800" href="' . $this->env()->projectUrl . '/cloud">
            <span class="text">' . $this->translate('Free trial expires in') . ' ' .$trialPeriodExpiresIn . ' ' . $this->translate('days') . '.</span>
          </a>
        ';
      }
    }

    return '';
  }

  public function generateDemoData(): void
  {
    $this->saveConfig('premiumAccountSince', date('Y-m-d H:i:s'));
    $this->saveConfig('subscriptionRenewalActive', '1');
    $this->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
    $this->saveConfig('freeTrialPeriodUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
  }
}
