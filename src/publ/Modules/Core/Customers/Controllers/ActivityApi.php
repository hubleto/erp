<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

use DateInterval;
use DateTime;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $dateStart = date("Y-m-d H:i:s", strtotime($this->params["start"]));
    $dateEnd = date("Y-m-d H:i:s", strtotime($this->params["end"]));

    $mAktivita = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent
      ->select("activities.*", "activity_types.color")
      ->join("activity_types", "activity_types.id", "=", "activities.id_activity_type")
      ->with("COMPANY")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
      ->where("activity_types.calendar_visibility", 1)
      ->get()
    ;
    $transformacia = [];

    foreach ($aktivity as $key => $aktivita) {

      $transformacia[$key]['id'] = $aktivita->id;
      if ($aktivita->time_start != null) $transformacia[$key]['start'] = $aktivita->date_start." ".$aktivita->time_start;
      else $transformacia[$key]['start'] = $aktivita->date_start;
      if ($aktivita->date_end != null) {
        if ($aktivita->time_end != null) $transformacia[$key]['end'] = $aktivita->date_end." ".$aktivita->time_end;
        else $transformacia[$key]['end'] = $aktivita->date_end;
      }
      /* else $transformacia[$key]['allDay'] = true; */
      $transformacia[$key]['title'] = $aktivita->subject;
      $transformacia[$key]['backColor'] = $aktivita->color;
      $transformacia[$key]['color'] = $aktivita->color;
      $transformacia[$key]['company'] = $aktivita->COMPANY->name;
    }

    return $transformacia;
  }
}
