import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormQuote from './FormQuote';

interface TableQuotesProps extends TableExtendedProps {
  idOrder?: number,
}

interface TableQuotesState extends TableExtendedState {
}

export default class TableQuotes extends TableExtended<TableQuotesProps, TableQuotesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Orders/Models/Quote',
  }

  props: TableQuotesProps;
  state: TableQuotesState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders';
  translationContextInner: string = 'Components\\TableQuotes';

  constructor(props: TableQuotesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableQuotesProps) {
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
      idOrder: this.props.idOrder,
    }
  }

  rowClassName(rowData: any): string {
    return '';
  }

  setRecordFormUrl(id: number) {
    window.history.pushState(
      {},
      "",
      globalThis.hubleto.config.projectUrl + '/quotes//' + (id > 0 ? id : 'add')
    );
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idOrder = this.props.idOrder;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_order: this.props.idOrder };
    return <FormQuote {...formProps}/>;
  }
}