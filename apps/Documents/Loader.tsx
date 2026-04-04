import App from '@hubleto/react-ui/core/App'
import TableFiles from "./Components/TableFiles"
import TableDocuments from "./Components/TableDocuments"
import TableDocumentVersions from "./Components/TableDocumentVersions"
import TableDocumentReviews from "./Components/TableDocumentReviews"
import TableTemplates from "./Components/TableTemplates"
import FileBrowser from "./Components/FileBrowser"

class DocumentsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DocumentsTableDocuments', TableDocuments);
    globalThis.hubleto.registerReactComponent('DocumentsTableDocumentVersions', TableDocumentVersions);
    globalThis.hubleto.registerReactComponent('DocumentsTableDocumentReviews', TableDocumentReviews);
    globalThis.hubleto.registerReactComponent('DocumentsTableFiles', TableFiles);
    globalThis.hubleto.registerReactComponent('DocumentsFileBrowser', FileBrowser);
    globalThis.hubleto.registerReactComponent('DocumentsTableTemplates', TableTemplates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Documents', new DocumentsApp());
