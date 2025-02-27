// import HubletoMain from "@hubleto/src/Main";
import BillingTableBillingAccountService from "./Components/TableBillingAccountServices"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('BillingTableBillingAccountService', BillingTableBillingAccountService);
  }
}
