import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

interface FormPermissionProps extends HubletoFormProps { }
interface FormPermissionState extends HubletoFormState { }

export default class FormPermission<P, S> extends HubletoForm<FormPermissionProps, FormPermissionState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Api/Models/Permission',
  }

  props: FormPermissionProps;
  state: FormPermissionState;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\FormPermission';

  constructor(props: FormPermissionProps) {
    super(props);
  }

  getRecordFormUrl(): string {
    return 'api/permissions/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Permission')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_key')}
          {this.inputWrapper('app')}
          {this.inputWrapper('controller')}
        </>;
      break;
    }
  }

}
