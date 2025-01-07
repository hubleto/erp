import HubletoCore from "../../../App";
import CalendarComponent from "./Components/CalendarComponent";
import CalendarMain from "./Components/CalendarMain";

export default class Loader {
  uid: string = 'calendar';
  constructor(app: HubletoCore) {
    app.registerReactComponent('CalendarComponent', CalendarComponent);
    app.registerReactComponent('CalendarMain', CalendarMain);
  }
}
