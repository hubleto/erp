<?php

/*
  This hook does not do anything. It is just an example of how the
  hook should be implemented.

  Hooks are very simple ways to automate your Hubleto. For more complex
  use cases, consider creating your own app.
*/

namespace HubletoMain\Hook;

class Example extends \HubletoMain\Core\Hook
{

  /* run()
    This method is invoked when a "hooks->run()" is called
    anywhere in the code.
  */

  public function run(string $event, array $args): void
  {
    if ($event == 'SEE-LIST-OF-AVAILABLE-TRIGGERS-IN-DEVELOPER-GUIDE') {
      // Do anything you want here.
      // Notes:
      //   - $this->main == reference to Hubleto's main object
      //   - structure of $args may vary depending on the event
      //   - you can find available events on https://developer.hubleto.com
      //     or simply search in the source code where method "hooks->run()" is
      //     called
      //   - learn by examples - see sample default hooks in 'Default' folder
      //   - you can create your own hooks in your app
    }
  }

}