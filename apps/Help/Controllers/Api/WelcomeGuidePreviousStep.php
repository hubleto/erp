<?php

namespace Hubleto\App\Community\Help\Controllers\Api;

use Hubleto\App\Community\Help\Loader;

class WelcomeGuidePreviousStep extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    /** @var Loader */
    $helpApp = $this->getService(Loader::class);

    $step = $this->config()->getAsInteger('help/welcomeGuidesStep', 0);
    $steps = $helpApp->getWelcomeGuideSteps();

    $step = max(0, $step - 1);

    $this->config()->saveForUser('help/welcomeGuidesStep', $step);

    return [
      "step" => $step,
      "html" => $steps[$step] ?? '',
    ];
  }

}
