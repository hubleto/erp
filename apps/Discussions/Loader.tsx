import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDiscussions from './Components/TableDiscussions'
import TableMembers from './Components/TableMembers';
import TableMessages from './Components/TableMessages';

class DiscussionsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('DiscussionsTableMembers', TableMembers);
    globalThis.main.registerReactComponent('DiscussionsTableDiscussions', TableDiscussions);
    globalThis.main.registerReactComponent('DiscussionsTableMessages', TableMessages);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Discussions', new DiscussionsApp());
