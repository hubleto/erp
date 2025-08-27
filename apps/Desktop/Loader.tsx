import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'

class DesktopApp extends HubletoApp {
  init() {
    super.init();
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Desktop', new DesktopApp());
