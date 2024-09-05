<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $mAktivita = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent->all();
    $transformacia = [];

    foreach ($aktivity as $key => $aktivity) {
      $transformacia[$key]["id"] = $aktivity->id;
      $transformacia[$key]["date"] = $aktivity->due_date;
      $transformacia[$key]["time"] = $aktivity->due_time;
      $transformacia[$key]["title"] = $aktivity->subject;
    }

    return $transformacia;
  }
}
