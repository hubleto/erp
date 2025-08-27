import React, { Component } from 'react'
import Table, { TableProps, TableState, TableDescription } from '@hubleto/react-ui/core/Table';

interface TableInvoiceItemsProps extends TableProps {
  idInvoice: any,
}

interface TableInvoiceItemsState extends TableState {
  idInvoice: any,
}

export default class TableInvoiceItems extends Table<TableInvoiceItemsProps, TableInvoiceItemsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 25,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Invoices/Models/InvoiceItem',
    // description: {
    //   ui: { addButtonText: this.translate('Create invoice') }
    // },
  }

  props: TableInvoiceItemsProps;
  state: TableInvoiceItemsState;

  translationContext: string = 'HubletoApp\\Community\\Invoices\\Loader::Components\\TableInvoiceItems';

  constructor(props: TableInvoiceItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props) as TableInvoiceItemsState;
  }

  getStateFromProps(props: TableInvoiceItemsProps) {
    return {
      ...super.getStateFromProps(props),
      idInvoice: props.idInvoice,
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
      idInvoice: this.state.idInvoice,
    }
  }

}