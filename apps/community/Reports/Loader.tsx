import HubletoMain from "../../../App";
import FormReport from "../../community/Reports/Components/FormReport";

export default class Loader {
  uid: string = 'report';
  constructor(app: HubletoMain) {
    console.log("report constructor");
    app.registerReactComponent('FormReport', FormReport);
  }
}
