import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormClick, { FormClickProps } from './FormClick';

interface TableClicksProps extends HubletoTableProps {
  idCampaign: number
}
interface TableClicksState extends HubletoTableState {}

export default class TableClicks extends HubletoTable<TableClicksProps, TableClicksState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Campaigns/Models/Click',
    orderBy: {field: 'datetime_clicked', direction: 'desc'},
  }

  props: TableClicksProps;
  state: TableClicksState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\TableClicks';

  constructor(props: TableClicksProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableClicksProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide',
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCampaign: this.props.idCampaign
    }
  }

  getCsvImportEndpointParams(): any {
    return {
      model: this.props.model,
      defaultCsvImportValues: {
        id_campaign: this.props.idCampaign,
      }
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/campaigns/clicks/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormClickProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign: this.props.idCampaign };
    return <FormClick {...formProps}/>;
  }
}