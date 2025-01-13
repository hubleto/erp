import HubletoMain from "../../../App";
import CoreDocumentsTableDocuments from "./Components/TableDocuments"

export default class Loader {
  uid: string = 'calendar';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreDocumentsTableDocuments', CoreDocumentsTableDocuments);
  }
}
