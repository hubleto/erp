import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

interface FormUsageProps extends HubletoFormProps { }
interface FormUsageState extends HubletoFormState { }

export default class FormUsage<P, S> extends HubletoForm<FormUsageProps, FormUsageState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Api/Models/Usage',
  }

  props: FormUsageProps;
  state: FormUsageState;

  translationContext: string = 'Hubleto\\App\\Community\\Api::Components\\FormUsage';

  constructor(props: FormUsageProps) {
    super(props);
  }

  getRecordFormUrl(): string {
    return 'api/usages/' + this.state.record.id;
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Usage</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_key')}
          {this.inputWrapper('controller')}
          {this.inputWrapper('used_on')}
          {this.inputWrapper('ip_address')}
        </>;
      break;
    }
  }

}
