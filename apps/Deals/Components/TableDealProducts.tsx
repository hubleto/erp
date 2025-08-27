import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import HubletoForm from '@hubleto/react-ui/ext/HubletoForm';

interface TableDealProductsProps extends HubletoTableProps {
  idDeal: number
}

interface TableDealProductsState extends HubletoTableState {}

export default class TableDealProducts extends HubletoTable<TableDealProductsProps, TableDealProductsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Deals/Models/DealProduct',
  }

  props: TableDealProductsProps;
  state: TableDealProductsState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\TableDealProducts';

  constructor(props: TableDealProductsProps) {
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