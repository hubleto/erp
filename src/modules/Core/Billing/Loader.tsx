import CeremonyCrmApp from "../../../App";
import CoreBillingTableBillingAccountService from "./Components/TableBillingAccountServices"

export default class Loader {
  uid: string = 'billing';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('CoreBillingTableBillingAccountService', CoreBillingTableBillingAccountService);
  }
}
