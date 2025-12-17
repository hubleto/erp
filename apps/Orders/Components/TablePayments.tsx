import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormPayment, { FormPaymentProps } from './FormPayment';
import request from '@hubleto/react-ui/core/Request';

interface TablePaymentsProps extends HubletoTableProps {
  idOrder?: number,
}

interface TablePaymentsState extends HubletoTableState {
}

export default class TablePayments extends HubletoTable<TablePaymentsProps, TablePaymentsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Orders/Models/Payment',
  }

  props: TablePaymentsProps;
  state: TablePaymentsState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\TablePayments';

  constructor(props: TablePaymentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TablePaymentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idOrder: this.props.idOrder,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/orders/payments/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "id_invoice_item") {
      console.log(data);
      if (!data['INVOICE_ITEM']) {
        return <button
          className='btn btn-transparent btn-small'
          onClick={() => {

            request.post('orders/api/prepare-payment-for-invoice',
              {
                idOrder: data['id_order'],
                idPayment: data['id'],
              },
              {},
              (data: any) => {
                this.reload();
              }
            );
          }}
        >
          <span className='text'>Prepare for invoice</span>
        </button>;
      } else {
        return super.renderCell(columnName, column, data, options);
      }
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }
  
  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormPaymentProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_order: this.props.idOrder };
    return <FormPayment {...formProps}/>;
  }
}