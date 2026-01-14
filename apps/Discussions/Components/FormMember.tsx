import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormMemberProps extends FormExtendedProps { }
interface FormMemberState extends FormExtendedState { }

export default class FormMember<P, S> extends FormExtended<FormMemberProps, FormMemberState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
      <small>{this.translate('Member')}</small>
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
