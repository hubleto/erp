import App from '@hubleto/react-ui/core/App'

import TableUsers from "./Components/FC/TableUsers"
import TableUserRoles from "./Components/FC/TableUserRoles"
import TableCurrencyRates from "./Components/FC/TableCurrencyRates"
import TableVatRates from "./Components/FC/TableVatRates"

import FormUser from "./Components/FC/FormUser"
import FormTeam from "./Components/FC/FormTeam"
import TableTeams from "./Components/FC/TableTeams"

class SettingsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('SettingsFormUser', FormUser);
    globalThis.hubleto.registerReactComponent('SettingsTableUsers', TableUsers);
    globalThis.hubleto.registerReactComponent('SettingsTableUserRoles', TableUserRoles);
    globalThis.hubleto.registerReactComponent('SettingsFormTeam', FormTeam);
    globalThis.hubleto.registerReactComponent('SettingsTableTeams', TableTeams);
    globalThis.hubleto.registerReactComponent('SettingsTableCurrencyRates', TableCurrencyRates);
    globalThis.hubleto.registerReactComponent('SettingsTableVatRates', TableVatRates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Custom/Settings', new SettingsApp());
