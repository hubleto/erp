<?php

namespace CeremonyCrmApp\Modules\Core\Settings;

class Loader extends \CeremonyCrmApp\Core\Module {
  public function __construct(\CeremonyCrmApp $app) {
    parent::__construct($app);

    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\User::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole::class);
  }
}