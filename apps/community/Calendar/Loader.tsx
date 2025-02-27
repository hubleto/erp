// import HubletoMain from "@hubleto/src/Main";
import CalendarComponent from "./Components/CalendarComponent";
import CalendarMain from "./Components/CalendarMain";

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('CalendarComponent', CalendarComponent);
    globalThis.main.registerReactComponent('CalendarMain', CalendarMain);
  }
}
