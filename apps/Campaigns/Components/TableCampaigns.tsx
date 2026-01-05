import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends HubletoTableProps {}
interface TableCampaignsState extends HubletoTableState {}

export default class TableCampaigns extends HubletoTable<TableCampaignsProps, TableCampaignsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Campaigns/Models/Campaign',
  }

  props: TableCampaignsProps;
  state: TableCampaignsState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\TableCampaigns';

  constructor(props: TableCampaignsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCampaignsProps) {
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

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/campaigns/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignProps;
    return <FormCampaign {...formProps}/>;
  }
}