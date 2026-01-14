import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormPayment, { FormPaymentProps } from './FormPayment';
import request from '@hubleto/react-ui/core/Request';
import moment from "moment";

interface TablePaymentsProps extends TableExtendedProps {
  idOrder?: number,
}

interface TablePaymentsState extends TableExtendedState {
}

export default class TablePayments extends TableExtended<TablePaymentsProps, TablePaymentsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/orders/payments/' + (id > 0 ? id : 'add'));
  }

  cellClassName(columnName: string, column: any, rowData: any) {
    const cellClassName = super.cellClassName(columnName, column, rowData);

    if (columnName == 'date_due') {
      const now = moment();
      const daysDue = moment(now).diff(moment(rowData['date_due']), 'days');
      console.log(rowData['date_due'], daysDue);
      if (daysDue >= 0) return cellClassName + ' text-red-800';
      else if (daysDue > -7) return cellClassName + ' text-yellow-800';
      else return cellClassName;
    } else {
      return cellClassName;
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "id_invoice_item") {
      if (data['INVOICE_ITEM']) {
        return <button
          className='btn btn-success btn-small'
        >
          <span className='icon'><i className='fas fa-check'></i></span>
          <span className='text'>{this.translate('Prepared for invoice')}</span>
        </button>;
      } else {
        return <button
          className='btn btn-yellow btn-small'
          onClick={() => {
            return globalThis.hubleto.showDialogConfirm(
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
          <span className='text'>{this.translate('Prepare for invoice')}</span>
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