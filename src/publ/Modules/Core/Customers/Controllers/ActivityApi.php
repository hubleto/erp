<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $mAktivita = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent->all();
    $transformacia = [];

    foreach ($aktivity as $aktivity) {
      $transformacia["{$aktivity->due_date}"]["date"] = $aktivity->due_date;
      $transformacia["{$aktivity->due_date}"]["time"] = $aktivity->due_time;
      $transformacia["{$aktivity->due_date}"]["title"] = $aktivity->subject;
    }

    return $transformacia;
  }
}
