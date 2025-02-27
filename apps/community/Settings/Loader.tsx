// import HubletoMain from "@hubleto/src/Main";
import SettingsFormUser from "./Components/FormUser"
import SettingsTablePipelines from "./Components/TablePipelines"
import SettingsTableUserRoles from "./Components/TableUserRoles"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('SettingsFormUser', SettingsFormUser);
    globalThis.main.registerReactComponent('SettingsTablePipelines', SettingsTablePipelines);
    globalThis.main.registerReactComponent('SettingsTableUserRoles', SettingsTableUserRoles);
  }
}
