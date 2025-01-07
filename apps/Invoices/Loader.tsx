import CeremonyCrmApp from "../../../App";
import CoreInvoicesTableInvoices from "./Components/TableInvoices"

export default class Loader {
  uid: string = 'invoices';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('CoreInvoicesTableInvoices', CoreInvoicesTableInvoices);
  }
}
