// import HubletoMain from "@hubleto/src/Main";
import DocumentsTableDocuments from "./Components/TableDocuments"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('DocumentsTableDocuments', DocumentsTableDocuments);
  }
}
