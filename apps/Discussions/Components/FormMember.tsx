import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormMemberProps extends HubletoFormProps { }
interface FormMemberState extends HubletoFormState { }

export default class FormMember<P, S> extends HubletoForm<FormMemberProps, FormMemberState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Discussions/Models/Team',
  }

  props: FormMemberProps;
  state: FormMemberState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\FormMember';

  constructor(props: FormMemberProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Member</small>
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
