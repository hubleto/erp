import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDiscussions from './Components/TableDiscussions'
import TableMembers from './Components/TableMembers';
import TableMessages from './Components/TableMessages';

class DiscussionsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DiscussionsTableMembers', TableMembers);
    globalThis.hubleto.registerReactComponent('DiscussionsTableDiscussions', TableDiscussions);
    globalThis.hubleto.registerReactComponent('DiscussionsTableMessages', TableMessages);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Discussions', new DiscussionsApp());
