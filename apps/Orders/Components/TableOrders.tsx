import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormOrder, { FormOrderProps } from './FormOrder';

interface TableOrdersProps extends HubletoTableProps {}

interface TableOrdersState extends HubletoTableState {
}

export default class TableOrders extends HubletoTable<TableOrdersProps, TableOrdersState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
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

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/orders/' + id);
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormOrderProps;
    return <FormOrder {...formProps}/>;
  }
}