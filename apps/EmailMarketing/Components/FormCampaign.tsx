import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableCampaignsSchedules from '@hubleto/apps/EmailMarketing/Components/TableCampaignsSchedules';
import TableRecipients from '@hubleto/apps/EmailMarketing/Components/TableRecipients';
import request from '@hubleto/react-ui/core/Request';

export interface FormCampaignProps extends FormExtendedProps {}
export interface FormCampaignState extends FormExtendedState {}

export default class FormCampaign<P, S> extends FormExtended<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Campaign',
    renderOwnerManagerUi: true,
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormCampaign';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  refTableRecipients: any = React.createRef();
  refEmails: any = React.createRef();

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        { uid: 'recipients', title: this.translate('Recipients') },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/campaigns/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2 flex-col md:flex-row'>
          <div className='grow'>
            {this.inputWrapper('title')}
            {this.inputWrapper('target_audience')}
            {this.inputWrapper('goal')}
            {this.inputWrapper('notes')}
            {this.inputWrapper('color')}
            {this.inputWrapper('id_workflow')}
            {this.inputWrapper('id_workflow_step')}
            {this.inputWrapper('id_owner')}
            {this.inputWrapper('id_manager')}
            {this.inputWrapper('is_closed')}
          </div>
          {this.state.id <= 0 ? null : <>
            <div className='grow'>
              <TableCampaignsSchedules
                tag='table_campaign_schedules'
                parentForm={this}
                uid={this.props.uid + "_table_campaign_schedules"}
                idCampaign={R.id}
              />
            </div>
          </>}
        </div>;
      break

      case 'recipients':
        return <div className='flex gap-2'>
          <div className='flex-3'>
            <TableRecipients
              tag='table_email_recipients'
              ref={this.refTableRecipients}
              parentForm={this}
              uid={this.props.uid + "_table_email_recipient"}
              idCampaign={R.id}
              view='briefOverview'
              onAfterLoadData={(table: any) => {
                this.setState({ recipients: table.state.data.records });
              }}
            />
          </div>
          <div className='flex-1 gap-2'>
            <div className='card'>
              <div className='card-header'>{this.translate('Import emails')}</div>
              <div className='card-body'>
                <textarea
                  className='w-full h-80'
                  placeholder={this.translate('One email per line.')}
                  ref={this.refEmails}
                ></textarea>
                <button
                  className='btn btn-add-outline mt-2 w-full'
                  onClick={() => {
                    request.post(
                      'email-marketing/api/import-emails',
                      {
                        idCampaign: R.id,
                        emails: this.refEmails.current.value,
                      },
                      {},
                      (data: any) => {
                        this.refTableRecipients.current.reload();
                      }
                    )
                  }}
                >
                  <span className='icon'><i className='fas fa-upload'></i></span>
                  <span className='text'>{this.translate('Import emails')}</span>
                </button>
              </div>
            </div>
            <div className='card'>
              <div className='card-body'>
                <button
                  className='btn btn-danger'
                  onClick={() => {
                    if (confirm('Are you sure to delete all recipients in this email?')) {
                      request.post(
                        'email-marketing/api/remove-all-recipients',
                        {
                          idCampaign: R.id                      },
                        {},
                        (data: any) => {
                          this.refTableRecipients.current.reload();
                        }
                      );
                    }
                  }}
                >
                  <span className='icon'><i className='fas fa-trash'></i></span>
                  <span className='text'>{this.translate('Remove all recipients')}</span>
                </button>
              </div>
            </div>
          </div>
        </div>;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

