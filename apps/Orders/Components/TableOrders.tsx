import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormOrder, { FormOrderProps } from './FormOrder';

interface TableOrdersProps extends TableExtendedProps {}

interface TableOrdersState extends TableExtendedState {
}

export default class TableOrders extends TableExtended<TableOrdersProps, TableOrdersState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Orders/Models/Order',
  }

  props: TableOrdersProps;
  state: TableOrdersState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\TableOrders';

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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/orders/' + (id > 0 ? id : 'add'));
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