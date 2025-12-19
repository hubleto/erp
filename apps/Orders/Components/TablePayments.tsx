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
      if (data['INVOICE_ITEM']) {
        return <button
          className='btn btn-success btn-small'
        >
          <span className='icon'><i className='fas fa-check'></i></span>
          <span className='text'>Already pepared</span>
        </button>;
      } else {
        return <button
          className='btn btn-yellow btn-small'
          onClick={() => {
      console.log(data);
            return globalThis.main.showDialogConfirm(
              <>
                <div>{data.ORDER?.identifier} ({data.ORDER?.title})</div>
                <div className='font-bold'>{data.title}</div>
                <div className='font-bold'>{data.unit_price} € x {data.amount} pcs</div>
                <div className='mt-4'>{this.translate('Is this payment ready to be invoiced? Press OK to confirm.')}</div>
              </>,
              {
                headerClassName: 'dialog-warning-header',
                contentClassName: 'dialog-warning-content',
                header: this.translate('Prepare for invoice'),
                yesText: this.translate('Yes, prepare for invoice'),
                yesButtonClass: 'btn-warning',
                onYes: () => {
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
                },
                noText: this.translate('Cancel'),
                noButtonClass: 'btn-transparent',
                onNo: () => {
                },
                onHide: () => {
                },
              }
            );
          }}
        >
          <span className='icon'><i className='fas fa-euro-sign'></i></span>
          <span className='text'>Prepare for invoice</span>
        </button>;
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