import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

export interface FormCashRegisterProps extends HubletoFormProps { }
export interface FormCashRegisterState extends HubletoFormState { }

export default class FormCashRegister<P, S> extends HubletoForm<FormCashRegisterProps, FormCashRegisterState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "Hubleto/App/Community/Cashdesk/Models/CashRegister"
  };

  props: FormCashRegisterProps;
  state: FormCashRegisterState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\Cashdesk\\Loader';
  translationContextInner: string = 'Components\\FormCashRegister';

  constructor(props: FormCashRegisterProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
    }
  }

  getStateFromProps(props: FormCashRegisterProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Cash register')}</b> },
      ],
    };
  }

  getRecordFormUrl(): string {
    return 'cashdesk/cash-registers/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['ITEMS'],
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Cash register')}</small>
      <h2>{this.state.record.identifier ? this.state.record.identifier : ''}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_company')}
          {this.inputWrapper('identifier')}
          {this.inputWrapper('description')}
        </>
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
  }

}
