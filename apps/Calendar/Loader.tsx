import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import CalendarMain from "./Components/CalendarMain";
import CalendarActivityForm from "./Components/FormActivity";
import CalendarShareTable from "./Components/CalendarShareTable";
import FormSharedCalendar from "./Components/FormSharedCalendar";

class CalendarApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('CalendarShareTable', CalendarShareTable);
    globalThis.main.registerReactComponent('CalendarMain', CalendarMain);
    globalThis.main.registerReactComponent('CalendarActivityForm', CalendarActivityForm);
    globalThis.main.registerReactComponent('SharedCalendarForm', FormSharedCalendar);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Calendar', new CalendarApp());

