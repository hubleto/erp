<?php

namespace CeremonyCrmApp\Controllers\App;

class Dashboard extends \CeremonyCrmApp\Core\Controller {

  public function prepareViewParams() {
    parent::prepareViewParams();

    // $mLokalita = new \EMonitorApp\Models\Lokalita($this->app);
    // $this->viewParams['lokality'] = $mLokalita->with('DATABAZY')->orderBy('name')->get()->toArray();

    // $lastOpenCostingModel = $mCM->getLastOpen();

    // $this->viewParams['lastOpenCostingModel'] = $lastOpenCostingModel;
    // $this->viewParams['lastOpenCostingModelTotalEstimatedCosts'] = $mCM->getTotalEstimatedCosts((int) $lastOpenCostingModel['id']);

  }
}
