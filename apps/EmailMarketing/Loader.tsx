import App from '@hubleto/react-ui/core/App'
import TableEmails from "./Components/TableEmails"
import TableEmailRecipients from "./Components/TableEmailRecipients"
import TableRecipientStatuses from "./Components/TableRecipientStatuses"

class EmailMarketingApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('EmailMarketingTableEmails', TableEmails);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableEmailRecipients', TableEmailRecipients);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableRecipientStatuses', TableRecipientStatuses);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/EmailMarketing', new EmailMarketingApp());
