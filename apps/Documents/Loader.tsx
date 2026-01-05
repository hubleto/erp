import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDocuments from "./Components/TableDocuments"
import TableTemplates from "./Components/TableTemplates"
import Browser from "./Components/Browser"

class DocumentsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DocumentsTableDocuments', TableDocuments);
    globalThis.hubleto.registerReactComponent('DocumentsBrowser', Browser);
    globalThis.hubleto.registerReactComponent('DocumentsTableTemplates', TableTemplates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Documents', new DocumentsApp());
