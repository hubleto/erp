// import HubletoMain from "@hubleto/src/Main";
import SalesTableLeads from "./Components/TableLeads"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('SalesTableLeads', SalesTableLeads);
  }
}
