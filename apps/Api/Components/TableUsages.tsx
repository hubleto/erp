import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormUsage from './FormUsage';

interface TableUsagesProps extends HubletoTableProps {
  idKey?: number,
}

interface TableUsagesState extends HubletoTableState {
}

export default class TableUsages extends HubletoTable<TableUsagesProps, TableUsagesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Api/Models/Usage',
    readonly: true,
  }

  props: TableUsagesProps;
  state: TableUsagesState;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\TableUsages';

  constructor(props: TableUsagesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableUsagesProps) {
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
      idKey: this.props.idKey
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/api/usages/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idKey = this.props.idKey
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_key: this.props.idKey };
    return <FormUsage {...formProps}/>;
  }
}