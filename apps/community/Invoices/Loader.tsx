// import HubletoMain from "@hubleto/src/Main";
import InvoicesTableInvoices from "./Components/TableInvoices"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('InvoicesTableInvoices', InvoicesTableInvoices);
  }
}
