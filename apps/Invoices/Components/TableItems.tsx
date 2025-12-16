import React, { Component } from 'react'
import Table, { TableProps, TableState, TableDescription } from '@hubleto/react-ui/core/Table';

interface TableItemsProps extends TableProps {
  idInvoice: any,
}

interface TableItemsState extends TableState {
  idInvoice: any,
}

export default class TableItems extends Table<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 25,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Invoices/Models/Item',
    // description: {
    //   ui: { addButtonText: this.translate('Create invoice') }
    // },
  }

  props: TableItemsProps;
  state: TableItemsState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\TableItems';

  constructor(props: TableItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props) as TableItemsState;
  }

  getStateFromProps(props: TableItemsProps) {
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

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/invoices/items/' + (id > 0 ? id : 'add'));
  }

}