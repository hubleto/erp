import CeremonyCrmApp from "../../../App";
import CoreDocumentsTableDocuments from "./Components/TableDocuments"

export default class Loader {
  uid: string = 'calendar';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('CoreDocumentsTableDocuments', CoreDocumentsTableDocuments);
  }
}
