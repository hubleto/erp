import HubletoCore from "../../../App";
import CoreDocumentsTableDocuments from "./Components/TableDocuments"

export default class Loader {
  uid: string = 'calendar';
  constructor(app: HubletoCore) {
    app.registerReactComponent('CoreDocumentsTableDocuments', CoreDocumentsTableDocuments);
  }
}
