import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormOrder, { FormOrderProps } from './FormOrder';
import request from '@hubleto/react-ui/core/Request';

interface TableOrdersProps extends TableProps {}

interface TableOrdersState extends TableState {
  tableOrderProductsDescription?: any,
}

export default class TableOrders extends Table<TableOrdersProps, TableOrdersState> {
  static defaultProps = {
    ...Table.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Orders/Models/Order',
  }

  props: TableOrdersProps;
  state: TableOrdersState;

  translationContext: string = 'HubletoApp\\Community\\Orders\\Loader::Components\\TableOrders';

  constructor(props: TableOrdersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableOrdersProps) {
    return {
      ...super.getStateFromProps(props),
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
    }
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  onAfterLoadTableDescription(description: any) {

    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Orders/Models/OrderProduct',
        idOrder: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableOrderProductsDescription: description} as TableOrdersState);
      }
    );

    return description;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormOrderProps;
    formProps.tableOrderProductsDescription = this.state.tableOrderProductsDescription;
    return <FormOrder {...formProps}/>;
  }
}