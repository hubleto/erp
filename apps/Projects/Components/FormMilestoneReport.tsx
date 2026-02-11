import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormMilestoneReportProps extends FormExtendedProps { }
interface FormMilestoneReportState extends FormExtendedState { }

export default class FormMilestoneReport<P, S> extends FormExtended<FormMilestoneReportProps, FormMilestoneReportState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/MilestoneReport',
  }

  props: FormMilestoneReportProps;
  state: FormMilestoneReportState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormMilestoneReport';

  constructor(props: FormMilestoneReportProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Milestone report')}</small>
      <h2>{this.state.record.date_report ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_milestone')}
          {this.inputWrapper('date_report')}
          {this.inputWrapper('summary')}
          {this.inputWrapper('details')}
          {this.inputWrapper('progress_percent')}
          {this.inputWrapper('id_reported_by')}
        </>;
      break;
    }
  }

}
