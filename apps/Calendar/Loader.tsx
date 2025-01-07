import HubletoMain from "../../../App";
import CalendarComponent from "./Components/CalendarComponent";
import CalendarMain from "./Components/CalendarMain";

export default class Loader {
  uid: string = 'calendar';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CalendarComponent', CalendarComponent);
    app.registerReactComponent('CalendarMain', CalendarMain);
  }
}
