import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormUsageProps extends FormExtendedProps { }
interface FormUsageState extends FormExtendedState { }

export default class FormUsage<P, S> extends FormExtended<FormUsageProps, FormUsageState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Api/Models/Usage',
  }

  props: FormUsageProps;
  state: FormUsageState;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\FormUsage';

  constructor(props: FormUsageProps) {
    super(props);
  }

  getRecordFormUrl(): string {
    return 'api/usages/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Usage')}</small>
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
          {this.inputWrapper('status')}
        </>;
      break;
    }
  }

}
