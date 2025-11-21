import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableRecipients from '@hubleto/apps/Campaigns/Components/TableRecipients';
import TableClicks from '@hubleto/apps/Campaigns/Components/TableClicks';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import WorkflowSelector, { updateFormWorkflowByTag } from '@hubleto/apps/Workflow/Components/WorkflowSelector';
import request from '@hubleto/react-ui/core/Request';
import InputJsonKeyValue from "@hubleto/react-ui/core/Inputs/JsonKeyValue";

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {
  testEmailVariables?: any,
  testEmailSendResult?: any,
  launchResult?: any,
  campaignWarnings?: any,
  recipients?: any,
}

export default class FormCampaign<P, S> extends HubletoForm<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/Campaign',
    renderWorkflowUi: true,
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\FormCampaign';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  refTestEmailRecipientInput: any;

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
    this.refTestEmailRecipientInput = React.createRef();
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        { uid: 'contacts', title: this.translate('Contacts') },
        { uid: 'recipients', title: this.translate('Recipients') },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'test', title: this.translate('Test') },
        { uid: 'launch', title: this.translate('Launch') },
        { uid: 'clicks', title: this.translate('Clicks') },
        ...this.getCustomTabs()
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  onTabChange() {
    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'launch':
        request.post(
          'campaigns/api/get-campaign-warnings',
          { idCampaign: this.state.record.id },
          {},
          (data: any) => {
            this.setState({campaignWarnings: data});
          }
        )
      break;
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign")}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  // renderTopMenu(): JSX.Element {
  //   return <>
  //     {super.renderTopMenu()}
  //     {this.state.id <= 0 ? null : <>
  //       <div className='flex-2 pl-4'><WorkflowSelector parentForm={this}></WorkflowSelector></div>
  //       {this.inputWrapper('is_closed', {wrapperCssClass: 'flex gap-2'})}
  //     </>}
  //   </>
  // }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('name')}
              {this.inputWrapper('target_audience')}
              {this.inputWrapper('goal')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('id_mail_account')}
              {this.inputWrapper('id_mail_template')}
              {this.inputWrapper('reply_to')}
              {this.inputWrapper('is_approved')}
              {/* {this.inputWrapper('mail_body')} */}
            </div>
            <div className='flex-1'>
              <div className='w-full flex gap-2'>
                {this.inputWrapper('utm_source')}
                {this.inputWrapper('utm_campaign')}
              </div>
              <div className='w-full flex gap-2'>
                {this.inputWrapper('utm_term')}
                {this.inputWrapper('utm_content')}
              </div>
              {this.inputWrapper('id_manager')}
              {this.inputWrapper('color')}
              {this.inputWrapper('datetime_created')}
              {this.inputWrapper('uid')}
            </div>
          </div>
        </>;
      break

      case 'contacts':
        return <div>
          <div>
            Select contacts which will be added as recipients
          </div>
          <TableContacts
            tag={"table_campaign_contact"}
            parentForm={this}
            uid={this.props.uid + "_table_campaign_contact"}
            selectionMode='multiple'
            readonly={true}
            descriptionSource='both'
            //@ts-ignore
            description={{ui: {showHeader: false}}}
            idCustomer={0}
            selection={R && R.RECIPIENTS ? R.RECIPIENTS.map((item) => { return { id: item.id_contact } }) : null}
            onSelectionChange={(table: any) => {
              request.post(
                'campaigns/api/save-recipients-from-contacts',
                {
                  idCampaign: this.state.record.id,
                  contactIds: table.state.selection.map((item) => item.id)
                },
                {},
                (result: any) => {
                  this.setState({record: {...R, RECIPIENTS: result}});
                }
              );
            }}
          />
        </div>;
      break;

      case 'recipients':
        return <TableRecipients
          tag='table_campaign_recipients'
          parentForm={this}
          uid={this.props.uid + "_table_campaign_recipient"}
          idCampaign={R.id}
          view='briefOverview'
          onAfterLoadData={(table: any) => {
            this.setState({ recipients: table.state.data.data });
          }}
        />;
      break;

      case 'tasks':
        return <TableTasks
          tag={"table_campaign_task"}
          parentForm={this}
          uid={this.props.uid + "_table_campaign_task"}
          junctionTitle='Campaign'
          junctionModel='Hubleto/App/Community/Campaigns/Models/CampaignTask'
          junctionSourceColumn='id_campaign'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_task'
        />;
      break;

      case 'test':
        return <>
          Test email recipient:
          <input
            ref={this.refTestEmailRecipientInput}
            className="ml-2"
            type="text"
            placeholder="Recipient email"
          />
          <br/>
          Test email variables:
          <InputJsonKeyValue uid="test-email-variables"
            onChange={(input: any, value: any) => {
              input.setState({value: value});
              this.setState({testEmailVariables: value});
            }}
          ></InputJsonKeyValue>
          <button
            className="btn btn-transparent mt-2"
            onClick={() => {
              request.post(
                'campaigns/api/send-test-email',
                {
                  idCampaign: this.state.record.id,
                  to: this.refTestEmailRecipientInput.current.value,
                  variables: this.state.testEmailVariables,
                },
                {},
                (result: any) => {
                  this.setState({testEmailSendResult: result})
                }
              );
            }}
          >
            <span className="icon"><i className="fas fa-envelope"></i></span>
            <span className="text">Send test email</span>
          </button>
          {this.state.testEmailSendResult && this.state.testEmailSendResult.status == 'success' ?
            <div className='alert alert-success mt-2'>Test email was sent to you.</div>
          : null}
          {this.state.testEmailSendResult && this.state.testEmailSendResult.status != 'success' ?
            <div className='alert alert-danger mt-2'>
              Error occured when sending a test email to you.
              <br/>
              <b>{this.state.testEmailSendResult.message}</b>
            </div>
          : null}
        </>;
      break;

      case 'launch':
        return <>
          {this.state.campaignWarnings ? <>
            {this.state.campaignWarnings.recentlyContacted
              && this.state.campaignWarnings.recentlyContacted.length > 0 ? <div className='alert alert-warning'>
              <b>Recently contacted</b>
              {this.state.campaignWarnings.recentlyContacted.map((item, key) => {
                return <div key={key}>
                  <code>
                    {item.CONTACT.first_name}&nbsp;{item.CONTACT.last_name}
                    &nbsp;
                    {item.CONTACT.VALUES ? item.CONTACT.VALUES.map((vItem, vKey) => {
                      if (vItem.type == 'email') {
                        return <span key={vKey}>{vItem.value}</span>;
                      } else {
                        return null;
                      }
                    }) : null}
                  </code> in
                  campaign <a
                    href={globalThis.main.config.projectUrl + '/campaigns/' + item.CAMPAIGN.id}
                    target='_blank'
                  >{item.CAMPAIGN.name}</a>.
                </div>;
              })}
            </div> : null}
          </> : null}

          {R.id_launched_by ? 
            <div className='alert alert-warning'>Campaign was already launched by {R.LAUNCHED_BY.email} on {R.datetime_launched}.</div>
          : null}

          {this.state.recipients ? <>
            <div className='flex flex-wrap mt-2 text-sm gap-2'>
              <b>Recipients:</b>
              {this.state.recipients ? this.state.recipients.map((item, key) => {
                return <span key={key}>{item.email}</span>;
              }) : null}
            </div>
            <div className='mt-2'>
              <button
                className="btn btn-primary-outline btn-large"
                onClick={() => {
                  request.post(
                    'campaigns/api/launch',
                    { idCampaign: this.state.record.id },
                    {},
                    (result: any) => {
                      this.setState({launchResult: result});
                      if (result.status && result.status == 'success') {
                        updateFormWorkflowByTag(this, 'campaign-launched', () => {
                          this.saveRecord();
                        });
                      }
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-paper-plane"></i></span>
                <span className="text">Send email to {this.state.recipients.length} recipients now!</span>
              </button>
              {this.state.launchResult && this.state.launchResult.status == 'success' ?
                <div className='alert alert-success mt-2'>Campaign was launched.</div>
              : null}
              {this.state.launchResult && this.state.launchResult.status != 'success' ?
                <div className='alert alert-danger mt-2'>
                  Error occured when launching the campaign.<br/>
                  <b>{this.state.launchResult.message}</b>
                </div>
              : null}
            </div>
          </> : <>
            <div className='alert alert-warning'>Campaign has no recipients. Add recipients first and then launch.</div>
          </>}
        </>;
      break;

      case 'clicks':
        return <TableClicks
          parentForm={this}
          tag="table_campaign_click"
          uid={this.props.uid + "_table_campaign_click"}
          idCampaign={R.id}
        />;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

