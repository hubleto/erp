import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableAddressesProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableAddressesState extends TableState {
}

export default class TableAddresses extends Table<TableAddressesProps, TableAddressesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Contacts/Models/Addresses',
  }

  props: TableAddressesProps;
  state: TableAddressesState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableAddresses';

  constructor(props: TableAddressesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

//   getStateFromProps(props: TableAddressesProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

//   renderForm(): JSX.Element {
//     let formDescription = this.getFormProps();
//     return <FormActivity {...formDescription}/>;
//   }
}