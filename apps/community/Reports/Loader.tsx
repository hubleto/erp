// import HubletoMain from "@hubleto/src/Main";
import FormReport from "../../community/Reports/Components/FormReport";

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('FormReport', FormReport);
  }
}
