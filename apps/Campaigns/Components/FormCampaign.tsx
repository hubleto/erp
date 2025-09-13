import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import WorkflowSelector, { updateFormWorkflowByTag } from '@hubleto/apps/Workflow/Components/WorkflowSelector';
import request from '@hubleto/react-ui/core/Request';

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {
  mailPreviewInfo?: any,
  testEmailSendResult?: any,
  campaignWarnings?: any,
}

export default class FormCampaign<P, S> extends HubletoForm<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/Campaign',
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader::Components\\FormCampaign';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        { uid: 'contacts', title: this.translate('Contacts'), showCountFor: 'CONTACTS' },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'launch', title: this.translate('Launch') },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/' + this.state.record.id;
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
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

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <WorkflowSelector
          idWorkflow={R.id_workflow}
          idWorkflowStep={R.id_workflow_step}
          onWorkflowChange={(idWorkflow: number, idWorkflowStep: number) => {
            this.updateRecord({id_workflow: idWorkflow, id_workflow_step: idWorkflowStep});
          }}
          onWorkflowStepChange={(idWorkflowStep: number, step: any) => {
            this.updateRecord({id_workflow_step: idWorkflowStep});
          }}
        ></WorkflowSelector>
        {this.inputWrapper('is_closed', {wrapperCssClass: 'flex gap-2'})}
      </>}
    </>
  }

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
              {this.inputWrapper('id_mail_account')}
              {this.inputWrapper('id_mail_template')}
              {this.inputWrapper('is_approved')}
              {/* {this.inputWrapper('mail_body')} */}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('utm_source')}
              {this.inputWrapper('utm_campaign')}
              {this.inputWrapper('utm_term')}
              {this.inputWrapper('utm_content')}
              {this.inputWrapper('id_manager')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('color')}
              {this.inputWrapper('datetime_created')}
              {this.inputWrapper('uid')}
            </div>
          </div>
        </>;
      break

      case 'contacts':
        const mailPreviewInfo: any = this.state.mailPreviewInfo;
        return <div className='flex gap-2'>
          <div className='flex-3 overflow-auto'>
            <TableContacts
              tag={"table_campaign_contact"}
              parentForm={this}
              uid={this.props.uid + "_table_campaign_contact"}
              selectionMode='multiple'
              readonly={true}
              selection={R.CONTACTS.map((item) => { return { id: item.id_contact } })}
              onSelectionChange={(table: any) => {
                request.post(
                  'campaigns/api/save-contacts',
                  {
                    idCampaign: this.state.record.id,
                    contactIds: table.state.selection.map((item) => item.id)
                  },
                  {},
                  (result: any) => {
                    this.setState({record: {...R, CONTACTS: result}});
                  }
                );
              }}
              onRowClick={(table: any, row: any) => {
                console.log('onRowClick', row);
                request.post(
                  'campaigns/api/get-mail-preview-info',
                  {
                    idCampaign: this.state.record.id,
                    idContact: row.id,
                  },
                  {},
                  (result: any) => {
                    console.log(result);
                    this.setState({mailPreviewInfo: result});
                  }
                );
              }}
            />
          </div>
          <div className='flex-2'>
            <div className='card card-info'>
              <div className='card-header'>Mail preview</div>
              <div className='card-body'>
                {mailPreviewInfo && mailPreviewInfo.CONTACT && mailPreviewInfo.bodyHtml != '' ? <>
                  <div><b>Contact</b></div>
                  <div className='text-sm bg-slate-100 p-2 flex flex-col'>
                    <div className='font-bold'>{mailPreviewInfo.CONTACT?.first_name}</div>
                    <div className='font-bold'>{mailPreviewInfo.CONTACT?.last_name}</div>
                    {mailPreviewInfo.CONTACT?.VALUES?.map((item, key) => {
                      return <div key={key}>{item.value}</div>;
                    })}
                  </div>
                  <div className='mt-2'><b>Preview</b></div>
                  <div
                    className='text-blue-800 max-h-72'
                    dangerouslySetInnerHTML={{__html: mailPreviewInfo.bodyHtml}}
                  ></div>
                  {mailPreviewInfo.MAIL ? <>
                    <div className='mt-2'><b>Send info</b></div>
                    <div>
                      Mail was sent on {mailPreviewInfo.MAIL.datetime_sent} from {mailPreviewInfo.MAIL.from}.
                    </div>
                  </> : null}
                </> : <div>
                  No mail preview available.
                </div>}
              </div>
            </div>
          </div>
        </div>
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

      case 'launch':
        return <div className='grid gap-2'>
          <div className='card'>
            <div className='card-header'>Test</div>
            <div className='card-body'>
              <button
                className="btn btn-transparent"
                onClick={() => {
                  request.post(
                    'campaigns/api/send-test-email-to-me',
                    { idCampaign: this.state.record.id },
                    {},
                    (result: any) => {
                      this.setState({testEmailSendResult: result})
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-envelope"></i></span>
                <span className="text">Send test email to me</span>
              </button>
              {this.state.testEmailSendResult && this.state.testEmailSendResult.status == 'success' ?
                <div className='alert alert-success mt-2'>Test email was sent to you.</div>
              : null}
              {this.state.testEmailSendResult && this.state.testEmailSendResult.status != 'success' ?
                <div className='alert alert-danger mt-2'>Error occured when sending a test email to you.</div>
              : null}
            </div>
          </div>

          <div className='card'>
            <div className='card-header'>Launch</div>
            <div className='card-body'>

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
              </> : null }

              <button
                className="btn btn-transparent"
                onClick={() => {
                  request.post(
                    'campaigns/api/launch',
                    { idCampaign: this.state.record.id },
                    {},
                    (result: any) => {
                      if (result.status && result.status == 'success') {
                        this.setState({launchResult: result}, () => {
                          updateFormWorkflowByTag(this, 'campaign-launched', () => {
                            this.saveRecord();
                          });
                        });
                      }
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-envelope"></i></span>
                <span className="text">Send email to {R.CONTACTS.length} contacts now!</span>
              </button>

              <div className='flex flex-wrap mt-2 text-sm gap-2'>
                {R.CONTACTS.map((item, key) => {
                  return <span key={key}>{item.CONTACT?.virt_email}</span>;
                })}
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

