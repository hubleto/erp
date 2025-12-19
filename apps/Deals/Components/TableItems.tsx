import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import HubletoForm from '@hubleto/react-ui/ext/HubletoForm';

interface TableItemsProps extends HubletoTableProps {
  idDeal: number
}

interface TableItemsState extends HubletoTableState {}

export default class TableItems extends HubletoTable<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Deals/Models/Item',
  }

  props: TableItemsProps;
  state: TableItemsState;

  translationContext: string = 'Hubleto\\App\\Community\\Deals\\Loader';
  translationContextInner: string = 'Components\\TableItems';

  constructor(props: TableItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idDeal: this.props.idDeal,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.description = {
      defaultValues: { id_deal: this.props.idDeal }
    }
    return <HubletoForm {...formProps}/>;
  }  
}