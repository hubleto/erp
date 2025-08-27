<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

class Templates extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'templates', 'content' => $this->translate('Templates') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Mail/Templates.twig');
  }

}
