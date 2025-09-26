import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table from "@hubleto/react-ui/core/Table";

export interface FormAccountProp extends HubletoFormProps {
}

export interface FormAccountState extends HubletoFormState {
}

export default class FormEntry<P, S> extends HubletoForm<FormAccountProp, FormAccountState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
  };

  props: FormAccountProp;
  state: FormAccountState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\BkAccounts\\Loader::Components\\FormEntry';

  constructor(props: FormAccountProp) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    // const showAdditional: boolean = R.id > 0 ? true : false;

    // const linkExists = this.state.description.defaultValues?.creatingForModel ? false : true

    return <>
      <div className="card">
        <div className="card-body">
          {this.inputWrapper('description')}
          <div>
            <div className="flex flex-wrap justify-between gap-2">
              <div className="flex flex-1">
                <div className="flex-1">{this.inputWrapper('amount')}</div>
                <div className="flex-1">{this.inputWrapper('status')}</div>
              </div>
              <div className="flex flex-1">
                <div className="flex-1">{this.inputWrapper('invoice_date')}</div>
                <div className="flex-1">{this.inputWrapper('due_date')}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>;
  }

}
