import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormCampaignSchedule, { FormCampaignScheduleProps } from './FormCampaignSchedule';

interface TableCampaignsSchedulesRecipientsProps extends TableExtendedProps {
  idCampaignSchedule?: number,
}
interface TableCampaignsSchedulesRecipientsState extends TableExtendedState {}

export default class TableCampaignsSchedulesRecipients extends TableExtended<TableCampaignsSchedulesRecipientsProps, TableCampaignsSchedulesRecipientsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/EmailMarketing/Models/CampaignScheduleRecipient',
  }

  props: TableCampaignsSchedulesRecipientsProps;
  state: TableCampaignsSchedulesRecipientsState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\TableCampaignsSchedulesRecipients';

  constructor(props: TableCampaignsSchedulesRecipientsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCampaignsSchedulesRecipientsProps) {
    return {
      ...super.getStateFromProps(props),
      idCampaignSchedule: this.props.idCampaignSchedule,
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCampaignSchedule: this.props.idCampaignSchedule
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/campaigns/schedules/recipients' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignScheduleProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign_schedule: this.props.idCampaignSchedule };
    return <FormCampaignSchedule {...formProps}/>;
  }
}