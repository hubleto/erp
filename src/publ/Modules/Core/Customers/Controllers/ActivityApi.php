<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

use DateInterval;
use DateTime;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $dateStart = date("Y-m-d", strtotime($this->params["start"]));
    $dateEnd = date("Y-m-d", strtotime($this->params["end"]));

    $mAktivita = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent
      ->leftJoin("activity_types", "activity_types.id", "=", "activities.id_activity_type")
      ->with("COMPANY")
      ->where("due_date", ">=", $dateStart)
      ->where("due_date", "<=", $dateEnd)
      ->where("activity_types.calendar_visibility", 1)
      ->get()
    ;
    $transformacia = [];

    foreach ($aktivity as $key => $aktivita) {
      //if ($aktivita->toArray()["ACTIVITY_TYPE"] == null) continue;
      $endTime = null;
      //výpočet trvania aktivity
      if ($aktivita->duration) {
        $endTime = new DateTime($aktivita->due_date." ".$aktivita->due_time);
        list($hours, $minutes, $seconds) = explode(':', $aktivita->duration);
        $interval = new DateInterval("PT{$hours}H{$minutes}M{$seconds}S");
        $endTime->add($interval);
        $endTime = $endTime->format('Y-m-d\TH:i:s');
      }

      $transformacia[$key]['id'] = $aktivita->id;
      $transformacia[$key]['start'] = $aktivita->due_date."T".$aktivita->due_time;
      if ($endTime) $transformacia[$key]['end'] = $endTime;
      else $transformacia[$key]['allDay'] = true;
      $transformacia[$key]['title'] = $aktivita->subject;
      $transformacia[$key]['color'] = $aktivita->color;
      $transformacia[$key]['company'] = $aktivita->COMPANY->name;
    }

    return $transformacia;
  }
}
