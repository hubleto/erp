import CeremonyCrmApp from "../../../App";
import CoreSettingsFormUser from "./Components/FormUser"
import CoreSettingsTablePipelines from "./Components/TablePipelines"
import CoreSettingsTableUserRoles from "./Components/TableUserRoles"

export default class Loader {
  uid: string = 'settings';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('CoreSettingsFormUser', CoreSettingsFormUser);
    app.registerReactComponent('CoreSettingsTablePipelines', CoreSettingsTablePipelines);
    app.registerReactComponent('CoreSettingsTableUserRoles', CoreSettingsTableUserRoles);
  }
}
