import App from '@hubleto/react-ui/core/App'
import CalendarMain from "./Components/CalendarMain";
import CalendarActivityForm from "./Components/FormActivity";
import CalendarShareTable from "./Components/CalendarShareTable";
import FormSharedCalendar from "./Components/FormSharedCalendar";

class CalendarApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CalendarShareTable', CalendarShareTable);
    globalThis.hubleto.registerReactComponent('CalendarMain', CalendarMain);
    globalThis.hubleto.registerReactComponent('CalendarActivityForm', CalendarActivityForm);
    globalThis.hubleto.registerReactComponent('SharedCalendarForm', FormSharedCalendar);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Calendar', new CalendarApp());

