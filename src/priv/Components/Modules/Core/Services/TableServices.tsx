import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableServicesProps extends TableProps {
  showHeader: boolean,
  showFooter: boolean
}

interface TableServicesState extends TableState {
}

export default class TableServices extends Table<TableServicesProps, TableServicesState> {
  static defaultProps = {
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Services/Models/Service',
  }

  props: TableServicesProps;
  state: TableServicesState;

  constructor(props: TableServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

//   getStateFromProps(props: TableServicesProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

//   renderForm(): JSX.Element {
//     let formDescription = this.getFormProps();
//     return <FormActivity {...formDescription}/>;
//   }
}