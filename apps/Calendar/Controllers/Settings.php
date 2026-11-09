<?php

namespace Hubleto\App\Community\Calendar\Controllers;

use Hubleto\App\Community\Auth\Models\User;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
      [ 'url' => 'calendar/settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var \Hubleto\App\Community\Calendar\Loader $calendarApp */
    $calendarApp = $this->getService(\Hubleto\App\Community\Calendar\Loader::class);

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');
    if ($settingsChanged) {
      $calendarApp->setUsersPermittedToViewOtherCalendars($this->router()->urlParamAsArray('usersPermittedToViewOtherCalendars'));
      $this->viewParams['settingsSaved'] = true;
    }

    /** @var User $mUser */
    $mUser = $this->getModel(User::class);

    $this->viewParams['users'] = $mUser->record->where('is_active', true)->orderBy('email')->get()->toArray();
    $this->viewParams['usersPermittedToViewOtherCalendars'] = $calendarApp->getUsersPermittedToViewOtherCalendars();

    $this->setView('@Hubleto:App:Community:Calendar/CalendarSettings.twig');
  }

}
