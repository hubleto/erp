import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import request from '@hubleto/react-ui/core/Request';

interface TableItemsProps extends HubletoTableProps {
  idInvoice?: number,
}

interface TableItemsState extends HubletoTableState {
}

export default class TableItems extends HubletoTable<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Invoices/Models/Item',
  }

  props: TableItemsProps;
  state: TableItemsState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\TableItems';

  constructor(props: TableItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableItemsProps) {
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
      idInvoice: this.props.idInvoice,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/invoices/items/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "id_invoice" && !data['INVOICE']) {
      return <button
        className='btn btn-yellow btn-small'
        onClick={() => {
          return globalThis.main.showDialogConfirm(
            <>
              <div className='font-bold'>{data.CUSTOMER.name}</div>
              <div className='font-bold'>{data.item}</div>
              <div className='font-bold'>{data.unit_price} € x {data.amount} pcs</div>
              <div className='mt-4'>{this.translate('Create invoice from this item? Press OK to confirm.')}</div>
            </>,
            {
              headerClassName: 'dialog-warning-header',
              contentClassName: 'dialog-warning-content',
              header: this.translate('Create invoice'),
              yesText: this.translate('Yes, create invoice'),
              yesButtonClass: 'btn-warning',
              onYes: () => {
                request.post('invoices/api/create-invoice-from-prepared-item',
                  {
                    idItem: data['id']
                  },
                  {},
                  (data: any) => {
                    if (!isNaN(data.idInvoice)) {
                      window.location.href = globalThis.main.config.projectUrl + '/invoices/' + data.idInvoice;
                    }
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
        <span className='text'>Create invoice</span>
      </button>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

}