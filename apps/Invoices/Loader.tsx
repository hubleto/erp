import HubletoCore from "../../../App";
import CoreInvoicesTableInvoices from "./Components/TableInvoices"

export default class Loader {
  uid: string = 'invoices';
  constructor(app: HubletoCore) {
    app.registerReactComponent('CoreInvoicesTableInvoices', CoreInvoicesTableInvoices);
  }
}
