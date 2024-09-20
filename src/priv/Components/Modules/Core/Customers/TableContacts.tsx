import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableContactsProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableContactsState extends TableState {
}

export default class TableContacts extends Table<TableContactsProps, TableContactsState> {
  static defaultProps = {
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Contact',
  }

  props: TableContactsProps;
  state: TableContactsState;

  constructor(props: TableContactsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

//   getStateFromProps(props: TableContactsProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

//   renderForm(): JSX.Element {
//     let formDescription = this.getFormProps();
//     return <FormActivity {...formDescription}/>;
//   }
}