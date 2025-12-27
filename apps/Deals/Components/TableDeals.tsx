import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormDeal, { FormDealProps } from './FormDeal';
import request from '@hubleto/react-ui/core/Request';

interface TableDealsProps extends HubletoTableProps {
  idCustomer?: number,
}

interface TableDealsState extends HubletoTableState {
}

export default class TableDeals extends HubletoTable<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Deals/Models/Deal',
  }

  props: TableDealsProps;
  state: TableDealsState;

  translationContext: string = 'Hubleto\\App\\Community\\Deals\\Loader';
  translationContextInner: string = 'Components\\TableDeals';

  constructor(props: TableDealsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealsProps) {
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
      idCustomer: this.props.idCustomer,
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "title") {
      return <>
        {super.renderCell(columnName, column, data, options)}
        {data['note'] ?
          <div
            className="badge badge-extra-small badge-warning block whitespace-pre truncate"
            style={{maxHeight: '2.7em', maxWidth: '20em', overflow: 'hidden'}}
          ><i className="fas fa-note-sticky mr-2"></i>{data['note']}</div>
        : null}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/deals/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormDealProps;
    formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    return <FormDeal {...formProps}/>;
  }
}