import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormExtended from '@hubleto/react-ui/ext/FormExtended';

interface TableItemsProps extends TableExtendedProps {
  idDeal: number
}

interface TableItemsState extends TableExtendedState {}

export default class TableItems extends TableExtended<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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
    return <FormExtended {...formProps}/>;
  }  
}