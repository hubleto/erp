<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers\Boards;

use Hubleto\App\Custom\LinkedinMessages\Counter;

class MessagesWithoutFollowup extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Counter */
    $counter = $this->getService(Counter::class);

    $items = $counter->queryForMessagesWithoutFollowup()
      ->orderBy('linkedin_messages.sent_at', 'desc')
      ->limit(50)
      ->get()
      ->toArray()
    ;

    $this->viewParams['title'] = $this->translate('LinkedIn messages without planned follow-up');
    $this->viewParams['titleCssClass'] = 'bg-red-400 p-2 text-white';
    $this->viewParams['items'] = $items;
    $this->viewParams['total'] = count($items);

    $this->setView('@Hubleto:App:Custom:LinkedinMessages/Boards/MessagesWithoutFollowup.twig');
  }

}
