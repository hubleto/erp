import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDocuments from "./Components/TableDocuments"
import TableTemplates from "./Components/TableTemplates"
import Browser from "./Components/Browser"

class DocumentsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('DocumentsTableDocuments', TableDocuments);
    globalThis.main.registerReactComponent('DocumentsBrowser', Browser);
    globalThis.main.registerReactComponent('DocumentsTableTemplates', TableTemplates);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Documents', new DocumentsApp());
