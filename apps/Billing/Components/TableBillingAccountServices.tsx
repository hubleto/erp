import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/components/cc/Table';

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
    model: 'Hubleto/App/Community/Billing/Models/BillingAccountService',
  }

  props: TableBillingAccountServicesProps = null;
  state: TableBillingAccountServicesState = null;

  translationContext: string = 'Hubleto\\App\\Community\\Billing\\Loader';
  translationContextInner: string = 'Components\\TableBillingAccountServices';

  constructor(props: TableBillingAccountServicesProps) {
    super(props);
    this.props = props;
    this.state = this.getStateFromProps(props);
  }
}