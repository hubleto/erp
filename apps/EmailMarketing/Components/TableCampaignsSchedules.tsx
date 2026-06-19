import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormCampaignSchedule, { FormCampaignScheduleProps } from './FormCampaignSchedule';

interface TableCampaignsSchedulesProps extends TableExtendedProps {
  idCampaign?: number,
}
interface TableCampaignsSchedulesState extends TableExtendedState {}

export default class TableCampaignsSchedules extends TableExtended<TableCampaignsSchedulesProps, TableCampaignsSchedulesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/EmailMarketing/Models/CampaignSchedule',
  }

  props: TableCampaignsSchedulesProps;
  state: TableCampaignsSchedulesState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\TableCampaignsSchedules';

  constructor(props: TableCampaignsSchedulesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCampaignsSchedulesProps) {
    return {
      ...super.getStateFromProps(props),
      idCampaign: this.props.idCampaign,
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/campaigns/schedule' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignScheduleProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign: this.props.idCampaign };
    return <FormCampaignSchedule {...formProps}/>;
  }
}