import HubletoMain from "../../../App";
import CoreInvoicesTableInvoices from "./Components/TableInvoices"

export default class Loader {
  uid: string = 'invoices';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreInvoicesTableInvoices', CoreInvoicesTableInvoices);
  }
}
