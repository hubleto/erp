<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class ActivityApi extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array {

    $dateStart = date("Y-m-d H:i:s", strtotime((string) $this->params["start"]));
    $dateEnd = date("Y-m-d H:i:s", strtotime((string) $this->params["end"]));

    $mAktivita = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $aktivity = $mAktivita->eloquent
      ->select("activities.*", "activity_types.color")
      ->join("activity_types", "activity_types.id", "=", "activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if (isset($this->params["creatingForModel"])) {
      if ($this->params["creatingForModel"] == "Company") {
        $aktivity
          ->join("company_activities", "company_activities.id_activity", "=", "activities.id")
          ->where("company_activities.id_company", $this->params["creatingForId"])
        ;
      } else if ($this->params["creatingForModel"] == "Lead"){
        $aktivity
          ->join("lead_activities", "lead_activities.id_activity", "=", "activities.id")
          ->where("lead_activities.id_lead", $this->params["creatingForId"])
        ;
      } else if ($this->params["creatingForModel"] == "Deal"){
        $aktivity
          ->join("deal_activities", "deal_activities.id_activity", "=", "activities.id")
          ->where("deal_activities.id_deal", $this->params["creatingForId"])
        ;
      }
    } else {
      //ak je hlavný kalendár
      $aktivity->where("activity_types.calendar_visibility", 1);
    }

    //var_dump($aktivity->toSql()); exit;

    $aktivity = $aktivity->get();
    $transformacia = [];

    foreach ($aktivity as $key => $aktivita) {

      $transformacia[$key]['id'] = $aktivita->id;
      if ($aktivita->time_start != null) $transformacia[$key]['start'] = $aktivita->date_start." ".$aktivita->time_start;
      else $transformacia[$key]['start'] = $aktivita->date_start;
      if ($aktivita->date_end != null) {
        if ($aktivita->time_end != null) $transformacia[$key]['end'] = $aktivita->date_end." ".$aktivita->time_end;
        else $transformacia[$key]['end'] = $aktivita->date_end;
      }
      $transformacia[$key]['allDay'] = $aktivita->all_day == 1 || $aktivita->time_start == null ? true : false;
      $transformacia[$key]['title'] = $aktivita->subject;
      $transformacia[$key]['backColor'] = $aktivita->color;
      $transformacia[$key]['color'] = $aktivita->color;
      $transformacia[$key]['company'] = $aktivita->COMPANY->name;
    }

    return $transformacia;
  }
}
