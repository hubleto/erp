import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableBillingAccountServicesProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableBillingAccountServicesState extends TableState {
}

export default class TableBillingAccountServices extends Table<TableBillingAccountServicesProps, TableBillingAccountServicesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Billing/Models/BillingAccountService',
  }

  props: TableBillingAccountServicesProps;
  state: TableBillingAccountServicesState;

  translationContext: string = 'HubletoApp\\Community\\Billing\\Loader::Components\\TableBillingAccountServices';

  constructor(props: TableBillingAccountServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

//   getStateFromProps(props: TableBillingAccountServicesProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

//   renderForm(): JSX.Element {
//     let formDescription = this.getFormProps();
//     return <FormActivity {...formDescription}/>;
//   }
}