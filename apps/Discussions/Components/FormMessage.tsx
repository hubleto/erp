import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormMessageProps extends HubletoFormProps { }
interface FormMessageState extends HubletoFormState { }

export default class FormMessage<P, S> extends HubletoForm<FormMessageProps, FormMessageState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Discussions/Models/Message',
  }

  props: FormMessageProps;
  state: FormMessageState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\FormMessage';

  constructor(props: FormMessageProps) {
    super(props);
  }

  getStateFromProps(props: FormMessageProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: this.translate('Task') },
        // Add your tabs here.
        // 'tab_with_nested_table': { title: 'Example tab with nested table' }
      ]
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Message')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
        </>;
      break;
    }
  }

}
