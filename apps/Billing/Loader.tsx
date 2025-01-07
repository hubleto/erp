import HubletoCore from "../../../App";
import CoreBillingTableBillingAccountService from "./Components/TableBillingAccountServices"

export default class Loader {
  uid: string = 'billing';
  constructor(app: HubletoCore) {
    app.registerReactComponent('CoreBillingTableBillingAccountService', CoreBillingTableBillingAccountService);
  }
}
