<?php

namespace Hubleto\App\Community\Help\Controllers\Api;

use Hubleto\App\Community\Help\Loader;

class WelcomeGuideNextStep extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    /** @var Loader */
    $helpApp = $this->getService(Loader::class);

    $steps = $helpApp->getWelcomeGuideSteps();

    $step = $this->config()->getAsInteger('help/welcomeGuidesStep', 0);
    $step = min(count($steps), $step + 1);

    $this->config()->saveForUser('help/welcomeGuidesStep', $step);

    return [
      "step" => $step,
      "html" => $steps[$step] ?? '',
    ];
  }

}
