import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table from "@hubleto/react-ui/core/Table";
import request from "@hubleto/react-ui/core/Request";

export interface FormTransactionProps extends HubletoFormProps {
}

export interface FormTransactionState extends HubletoFormState {
  reconciledAmount?: number
}

export default class FormTransaction<P, S> extends HubletoForm<FormTransactionProps, FormTransactionState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "Hubleto/App/Community/Transactions/Models/Transaction"
  };

  props: FormTransactionProps;
  state: FormTransactionState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\Transactions\\Loader::Components\\FormTransaction';

  constructor(props: FormTransactionProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      reconciledAmount: 0,
      ...this.getStateFromProps(props),
    }
  }

  getEndpointParams(): object {
    let params = super.getEndpointParams();

    return params;
  }

  getReconciledAmount(record: any ): any {
    if (record.id <= 0) return;

    request.post('transactions/api/get-reconciled-amount', {transactionId: record.id}, {},
      (response: any) => {
        this.setState({reconciledAmount: response.reconciledAmount});
      })

  }

  onAfterRecordLoaded(record: any): any {
    this.getReconciledAmount(record);

    return super.onAfterRecordLoaded(record);
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    // const showAdditional: boolean = R.id > 0 ? true : false;

    // const linkExists = this.state.description.defaultValues?.creatingForModel ? false : true

    return <>
      <div className="card">
        <div className="card-body">
          {this.inputWrapper('bank')}
          {this.inputWrapper('date')}
          {this.inputWrapper('description')}
          {this.inputWrapper('amount')}
        </div>
      </div>

      <div className="card bg-gray-50">
        <div className="card-body">
          <div className="flex justify-between items-center">
            <div className="input-wrapper">
              <span className="input-label">Total amount:</span>
              <span className="font-bold">{R.amount}</span>
            </div>
            <div className="input-wrapper">
              <span className="input-label">Reconciled Amount:</span>
              <span className="font-bold">{this.state.reconciledAmount}</span>
            </div>
            <div>
              {this.inputWrapper('isReconciled')}
            </div>
          </div>
        </div>
      </div>

      { R.id > 0 &&

        <div className="card mt-4">
          <div className="card-body">
            <Table model="Hubleto/App/Community/Transactions/Models/Reconciliation" formProps={{
              model: 'Hubleto/App/Community/Transactions/Models/Reconciliation',
            }} uid={this.props.uid} customEndpointParams={{ idTransaction: R.id}}></Table>
          </div>
        </div>
      }
    </>;
  }

}
