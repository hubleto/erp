import CeremonyCrmApp from "../../../App";
import CalendarComponent from "./Components/CalendarComponent";

export default class Loader {
  uid: string = 'calendar';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('CalendarComponent', CalendarComponent);
  }
}
