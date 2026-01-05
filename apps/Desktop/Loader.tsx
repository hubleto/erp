import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'

class DesktopApp extends HubletoApp {
  init() {
    super.init();
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Desktop', new DesktopApp());
