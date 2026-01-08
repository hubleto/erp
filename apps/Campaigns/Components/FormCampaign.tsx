import React, { Component, ChangeEvent } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableRecipients from '@hubleto/apps/Campaigns/Components/TableRecipients';
import TableClicks from '@hubleto/apps/Campaigns/Components/TableClicks';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import request from '@hubleto/react-ui/core/Request';
import InputJsonKeyValue from "@hubleto/react-ui/core/Inputs/JsonKeyValue";
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import CampaignFormActivity, { CampaignFormActivityProps, CampaignFormActivityState } from './CampaignFormActivity';
import moment, { Moment } from "moment";
import Calendar from '../../Calendar/Components/Calendar';
import { updateFormWorkflowByTag } from '@hubleto/react-ui/ext/WorkflowSelector';

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {
  testEmailVariables?: any,
  testEmailSendResult?: any,
  launchResult?: any,
  campaignWarnings?: any,
  recipients?: any,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
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
  refLogActivityInput: any;
  refActivityModal: any;
  refActivityForm: any;

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
    this.refTestEmailRecipientInput = React.createRef();
    this.refLogActivityInput = React.createRef();
    this.refActivityModal = React.createRef();
    this.refActivityForm = React.createRef();
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        { uid: 'calendar', title: this.translate('Calendar') },
        { uid: 'contacts', title: this.translate('Contacts') },
        { uid: 'recipients', title: this.translate('Recipients') },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'test', title: this.translate('Test') },
        { uid: 'launch', title: this.translate('Launch') },
        { uid: 'clicks', title: this.translate('Clicks') },
        { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
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

  logCompletedActivity() {
    request.get(
      'campaigns/api/log-activity',
      {
        idCampaign: this.state.record.id,
        activity: this.refLogActivityInput.current.value,
      },
      (result: any) => {
        this.loadRecord();
        this.refLogActivityInput.current.value = '';
      }
    );
  }

  scheduleActivity() {
    this.setState({
      showIdActivity: -1,
      activityDate: moment().add(1, 'week').format('YYYY-MM-DD'),
      activityTime: moment().add(1, 'week').format('H:00:00'),
      activitySubject: this.refLogActivityInput.current.value,
      activityAllDay: false,
    } as FormCampaignState);
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
              {this.inputWrapper('id_owner')}
              {this.inputWrapper('id_manager')}
              {this.inputWrapper('color')}
              {this.inputWrapper('shared_folder')}
              {this.inputWrapper('datetime_created')}
              {this.inputWrapper('uid')}
            </div>
          </div>
        </>;
      break

      case 'calendar':
        //@ts-ignore
        const tmpCalendarSmall = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_closed}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?source=campaigns&idCampaign=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormCampaignState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormCampaignState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        const recentActivitiesAndCalendar = <div className='card card-body flex flex-col gap-2'>
          <div>
            {tmpCalendarSmall}
          </div>
          <div>
            <div className="hubleto component input"><div className="input-element w-full flex gap-2">
              <input
                className="w-full bg-blue-50 border border-blue-800 p-1 text-blue-800 placeholder-blue-300"
                placeholder={this.translate('Type recent activity here')}
                ref={this.refLogActivityInput}
                onKeyUp={(event: any) => {
                  if (event.keyCode == 13) {
                    if (event.shiftKey) {
                      this.scheduleActivity();
                    } else {
                      this.logCompletedActivity();
                    }
                  }
                }}
                onChange={(event: ChangeEvent<HTMLInputElement>) => {
                  this.refLogActivityInput.current.value = event.target.value;
                }}
              />
            </div></div>
            <div className='mt-2'>
              <button onClick={() => {this.logCompletedActivity()}} className="btn btn-blue-outline btn-small w-full">
                <span className="icon"><i className="fas fa-check"></i></span>
                <span className="text">{this.translate('Log completed activity')}</span>
                <span className="shortcut">{this.translate('Enter')}</span>
              </button>
              <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
                <span className="icon"><i className="fas fa-clock"></i></span>
                <span className="text">{this.translate('Schedule activity')}</span>
                <span className="shortcut">{this.translate('Shift+Enter')}</span>
              </button>
            </div>
            {this.divider(this.translate('Most recent activities'))}
            {R.ACTIVITIES ? <div className="list">{R.ACTIVITIES.reverse().slice(0, 7).map((item, index) => {
              return <>
                <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
                  onClick={() => this.setState({showIdActivity: item.id} as FormCampaignState)}
                >
                  <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
                  <span className="text">
                    {item.subject}
                    {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
                  </span>
                </button>
              </>
            })}</div> : null}
          </div>
        </div>;

        //@ts-ignore
        const tmpCalendarLarge = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_closed}
          initialView='timeGridWeek'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?source=campaigns&idCampaign=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormCampaignState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormCampaignState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        return <>
          <div className='flex gap-2 mt-2'>
            <div className='flex-2 w-2/3'>
              {tmpCalendarLarge}
            </div>
            <div className='flex-1 w-1/3'>
              {this.state.id > 0 ? recentActivitiesAndCalendar : null}
            </div>
          </div>
          {this.state.showIdActivity == 0 ? null :
            <ModalForm
              ref={this.refActivityModal}
              form={this.refActivityForm}
              uid='activity_form'
              isOpen={true}
              type='right'
            >
              <CampaignFormActivity
                modal={this.refActivityModal}
                ref={this.refActivityForm}
                id={this.state.showIdActivity}
                isInlineEditing={true}
                description={{
                  defaultValues: {
                    id_campaign: R.id,
                    date_start: this.state.activityDate,
                    time_start: this.state.activityTime == "00:00:00" ? null : this.state.activityTime,
                    date_end: this.state.activityDate,
                    all_day: this.state.activityAllDay,
                    subject: this.state.activitySubject,
                  }
                }}
                idCustomer={R.id_customer}
                onClose={() => { this.setState({ showIdActivity: 0 } as FormCampaignState) }}
                onSaveCallback={(form: CampaignFormActivity<CampaignFormActivityProps, CampaignFormActivityState>, saveResponse: any) => {
                  if (saveResponse.status == "success") {
                    this.setState({ showIdActivity: 0 } as FormCampaignState);
                  }
                }}
              ></CampaignFormActivity>
            </ModalForm>
          }
        </>;
      break;

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
            <span className="text">{this.translate('Send test email')}</span>
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
        console.log(R.RECIPIENTS);
        return <>
          {this.state.campaignWarnings ? <>
            {this.state.campaignWarnings.recentlyContacted
              && this.state.campaignWarnings.recentlyContacted.length > 0 ? <div className='alert alert-warning'>
              <b>{this.translate('Recently contacted')}</b>
              {this.state.campaignWarnings.recentlyContacted.map((item, key) => {
                if (!item.CONTACT) return null;
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
                    href={globalThis.hubleto.config.projectUrl + '/campaigns/' + item.CAMPAIGN.id}
                    target='_blank'
                  >{item.CAMPAIGN.name}</a>.
                </div>;
              })}
            </div> : null}
          </> : null}

          {R.id_launched_by ? 
            <div className='alert alert-warning'>Campaign was already launched by {R.LAUNCHED_BY.email} on {R.datetime_launched}.</div>
          : null}

          {R.RECIPIENTS ? <div className='flex gap-2 w-full'>
            <div className='card grow'>
              <div className='card-header'>Recipients</div>
              <div className='card-body'>
                {R.RECIPIENTS ? R.RECIPIENTS.map((item, key) => {
                  return <div className='flex gap-2 items-center'>
                    <div key={key}>{item.email}</div>
                    {item.id_mail > 0 ? (
                      item.MAIL?.datetime_sent
                        ? <div className='badge badge-success'>Email was sent on {item.MAIL?.datetime_sent}</div>
                        : <div className='badge badge-warning'>Email scheduled for {item.MAIL?.datetime_scheduled_to_send}</div>
                    ) : null}
                    {item.STATUS?.is_opted_out ? <div className='badge badge-danger'>Opted out</div> : null}
                    {item.STATUS?.is_invalid ? <div className='badge badge-warning'>Invalid</div> : null}
                  </div>
                }) : null}
              </div>
            </div>
            <div className='grow'>
              <button
                className="btn btn-add-outline btn-large"
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
                          this.reload();
                        });
                      }
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-paper-plane"></i></span>
                <span className="text">Launch campaign now!</span>
              </button>
              <div className='mt-2 alert alert-info'>
                Emails will be sent only to recipients who did not receive email from
                this campaign yet.
              </div>
              <div className='mt-2 alert alert-info'>
                Opted-out recipients will be ignored.
              </div>
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
          </div> : <>
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

      case 'timeline':
        return this.renderTimeline([
          {
            data: (thisForm) => thisForm.state.record.ACTIVITIES,
            icon: 'fas fa-calendar',
            color: '#32678fff',
            timestampFormatter: (entry) => entry.date_start,
            valueFormatter: (entry) => entry.subject,
            userNameFormatter: (entry) => entry['_LOOKUP[id_owner]'],
          },
          { 
            data: (thisForm) => thisForm.state.record.WORKFLOW_HISTORY,
            icon: 'fas fa-timeline',
            color: '#8f3248ff',
            timestampFormatter: (entry) => entry.datetime_change,
            valueFormatter: (entry) => entry.WORKFLOW_STEP?.name ?? '---',
            userNameFormatter: (entry) => entry.USER?.nick,
          },
        ]);
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

