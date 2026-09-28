<?php

namespace Hubleto\App\Community\Help;

class Loader extends \Hubleto\Erp\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public array $contextHelp = [];

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^help\/?$/' => Controllers\Help::class,
      '/^help\/api\/welcome-guide-previous-step\/?$/' => Controllers\Api\WelcomeGuidePreviousStep::class,
      '/^help\/api\/welcome-guide-next-step\/?$/' => Controllers\Api\WelcomeGuideNextStep::class,
      '/^help\/search\/?$/' => Controllers\Search::class,
    ]);

    $this->contextHelp = $this->collectExtendibles('ContextHelp');
  }

  public function getWelcomeGuideSteps(): array
  {
    return [
      0 => '
        Go to <a href="customers" class="btn btn-white" target="_blank"><span class="text">Customers</span></a>
        app and click <button class="btn btn-white"><span class="text">Add customer</span></button> to add your first customer.
      ',
      1 => '
        Open your <a href="customers/1" class="btn btn-white" target="_blank"><span class="text">first customer</span></a>
        or open <a href="contacts" target="_blank" class="btn btn-white"><span class="text">Contacts</span></a> app
        and add <u>primary</u> contact for this customer.
      ',
      2 => '
        Change <a href="workflow/workflows" class="btn btn-white" target="_blank"><span class="text">default workflows</span></a>
        if you need to.
      ',
      3 => '
        Create a <a href="deals/add" class="btn btn-white" target="_blank"><span class="text">Deal</span></a>
        and assign it to your customer. Do not forget to set the deal\'s workflow.
      ',
      4 => '
        Track the progress of your deal in <a href="workflow" class="btn btn-white" target="_blank"><span class="text">Workflow</span></a> app.
      ',
      5 => '
        Plan follow-ups for the deal to win it. <a href="deals/1" class="btn btn-white" target="_blank"><span class="text">Open the deal</span></a>
        and go to <button class="btn btn-white"><span class="text">Calendar</span></button> tab. Schedule an
        activity there.
      ',
      6 => '
        You won the deal! Now, <a href="deals/1" class="btn btn-white" target="_blank"><span class="text">open the deal</span></a> and
        click <button class="btn btn-white"><span class="text">Create order</span></button> to create an order from this deal.
      ',
      7 => '
        If you need to track time spent on the order, <a href="orders/1" class="btn btn-white" target="_blank"><span class="text">open the order</span></a> and click
        <button class="btn btn-white"><span class="text">Create project</span></button>.
        Then create a task.
      ',
      8 => '
        Go to <a href="worksheets" class="btn btn-white" target="_blank"><span class="text">Worksheets</span></a>
        app and click <button class="btn btn-white"><span class="text">Add activity</span></button> to track your time.
      ',
      9 => '
        You did the job. Now it is time to issue an invoice. Open the deal, go to <i>Items</i>
        tab, create the item and click <i>Prepare for invoice</i>. Then go to <i>Invoices</i>
        app and create the invoice.
      ',
    ];
  }

  /**
   * [Description for getWelcomeScreenMessages]
   *
   * @return array
   * 
   */
  public function getWelcomeScreenMessages(): array
  {
    $hidden = $this->config()->getAsBool('help/welcomeGuideHidden', false);
    $step = $this->config()->getAsInteger('help/welcomeGuidesStep', 0);

    $steps = $this->getWelcomeGuideSteps();

    $messages = [];

    if (!$hidden && isset($steps[$step])) {
      $messages[] = [
        'class' => 'lime',
        'icon' => 'fas fa-graduation-cap',
        'content' => '
          <div><b>Learn Hubleto in few steps</b></div>
          <div class="mt-2 step">' . $steps[$step] . '</div>
          <div class="mt-2">
            <button
              class="btn btn-white"
              onclick="
                let t = $(this);
                $.getJSON(
                  \'help/api/welcome-guide-previous-step\',
                  function(data, status) {
                    let html = \'\';
                    if (data.html == \'\') {
                      html = \'Well done. 👍 You are now ready to start using Hubleto.\';
                    } else {
                      html = data.html;
                    }
                    t.closest(\'.content\').find(\'.step\').html(html);
                  }
                );
              "
            > 
              <span class="icon"><i class="fas fa-arrow-left"></i></span>
              <span class="text">Previous</span>
            </button>
            <button
              class="btn btn-white"
              onclick="
                let t = $(this);
                $.getJSON(
                  \'help/api/welcome-guide-next-step\',
                  function(data, status) {
                    let html = \'\';
                    if (data.html == \'\') {
                      html = \'Well done. 👍 You are now ready to start using Hubleto.\';
                    } else {
                      html = data.html;
                    }
                    t.closest(\'.content\').find(\'.step\').html(html);
                  }
                );
              "
            >
              <span class="icon"><i class="fas fa-arrow-right"></i></span>
              <span class="text">Next</span>
            </button>
          </div>
        ',
      ];
    }

    return $messages;
  }

}
