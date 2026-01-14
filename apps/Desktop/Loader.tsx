import App from '@hubleto/react-ui/core/App'

class DesktopApp extends App {
  init() {
    super.init();
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Desktop', new DesktopApp());
