import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends TableExtendedProps {}
interface TableCampaignsState extends TableExtendedState {}

export default class TableCampaigns extends TableExtended<TableCampaignsProps, TableCampaignsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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