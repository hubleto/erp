import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormKey from './FormKey';

interface TableKeysProps extends HubletoTableProps {
  idKey?: number,
}

interface TableKeysState extends HubletoTableState {
}

export default class TableKeys extends HubletoTable<TableKeysProps, TableKeysState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Api/Models/Key',
  }

  props: TableKeysProps;
  state: TableKeysState;

  translationContext: string = 'Hubleto\\App\\Community\\Api';
  translationContextInner: string = 'Components\\TableKeys';

  constructor(props: TableKeysProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableKeysProps) {
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
      idKey: this.props.idKey,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/api/keys/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "key") {
      return data.key.substr(0, 8) + ' ...';
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idKey = this.props.idKey;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_key: this.props.idKey };
    return <FormKey {...formProps}/>;
  }
}