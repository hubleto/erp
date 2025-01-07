import HubletoMain from "../../../App";
import CoreBillingTableBillingAccountService from "./Components/TableBillingAccountServices"

export default class Loader {
  uid: string = 'billing';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreBillingTableBillingAccountService', CoreBillingTableBillingAccountService);
  }
}
