import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormShop from './FormShop';

interface TableShopsProps extends TableExtendedProps { }

interface TableShopsState extends TableExtendedState { }

export default class TableShops extends TableExtended<TableShopsProps, TableShopsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Shops/Models/Shop',
  }

  props: TableShopsProps;
  state: TableShopsState;

  translationContext: string = 'Hubleto\\App\\Community\\Shops\\Loader';
  translationContextInner: string = 'Components\\TableShops';

  refActivityModal: any = null;

  constructor(props: TableShopsProps) {
    super(props);
    this.refActivityModal = React.createRef();
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableShopsProps) {
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/shops/' + (id > 0 ? id : 'add'));
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormShop {...formProps}/>;
  }
}