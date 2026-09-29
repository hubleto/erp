<?php declare(strict_types=1);

namespace Hubleto\App\Community\Notifications\EventListeners;

use Hubleto\App\Community\Notifications\Sender;
use Hubleto\Framework\Interfaces\ModelInterface;

class EmailNotifications extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onSendEmailNotification(string $template, array $vars): void
  {
    // TBD...
  }

}