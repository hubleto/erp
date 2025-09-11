<?php

namespace Hubleto\App\Community\Accounts;

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
      '/^accounts\/receivable?$/' => Controllers\Receivable::class,
      '/^accounts\/receivable\/add?$/' => ['controller' => Controllers\Receivable::class, 'vars' => ['recordId' => -1]],

      '/^accounts\/payable?$/' => Controllers\Payable::class,
      '/^accounts\/payable\/add?$/' => ['controller' => Controllers\Payable::class, 'vars' => ['recordId' => -1]],
    ]);

//    /** @var \Hubleto\App\Community\Pipeline\Manager $pipelineManager */
//    $pipelineManager = $this->getService(\Hubleto\App\Community\Pipeline\Manager::class);
//    $pipelineManager->addPipeline($this, 'invoices', Pipeline::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Receivable::class)->dropTableIfExists()->install();
      $this->getModel(Models\Payable::class)->dropTableIfExists()->install();
    }
  }

}