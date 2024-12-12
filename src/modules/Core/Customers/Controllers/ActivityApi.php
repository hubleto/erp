<?php

namespace CeremonyCrmMod\Core\Customers\Controllers;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $dateStart = date("Y-m-d H:i:s", strtotime((string) $this->app->params["start"]));
    $dateEnd = date("Y-m-d H:i:s", strtotime((string) $this->app->params["end"]));

    $mAktivita = new \CeremonyCrmMod\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent
      ->select("activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->join("activity_types", "activity_types.id", "=", "activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
      ->where("activities.id_user", $this->app->auth->user["id"])
    ;

    //ak je hlavný kalendár
    if (!isset($this->app->params["creatingForModel"])) {
      $aktivity->where("activity_types.calendar_visibility", 1);
    }

    $aktivity = $aktivity->get();
    $transformacia = [];

    foreach ($aktivity as $key => $aktivita) {

      $transformacia[$key]['id'] = $aktivita->id;
      if ($aktivita->time_start != null) $transformacia[$key]['start'] = $aktivita->date_start." ".$aktivita->time_start;
      else $transformacia[$key]['start'] = $aktivita->date_start;
      if ($aktivita->date_end != null) {
        if ($aktivita->time_end != null) $transformacia[$key]['end'] = $aktivita->date_end." ".$aktivita->time_end;
        else $transformacia[$key]['end'] = $aktivita->date_end;
      } else if ($aktivita->time_end != null) {
        $transformacia[$key]['end'] = $aktivita->date_start." ".$aktivita->time_end;
      }
      $transformacia[$key]['allDay'] = $aktivita->all_day == 1 || $aktivita->time_start == null ? true : false;
      $transformacia[$key]['title'] = $aktivita->subject;
      $transformacia[$key]['backColor'] = $aktivita->color;
      $transformacia[$key]['color'] = $aktivita->color;
      $transformacia[$key]['type'] = $aktivita->activity_type;
    }

    return $transformacia;
  }
}
