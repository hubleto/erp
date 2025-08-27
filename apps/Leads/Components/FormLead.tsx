import React, { Component, createRef, ChangeEvent } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import InputTags2 from '@hubleto/react-ui/core/Inputs/Tags2';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';
import Calendar from '../../Calendar/Components/Calendar';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import LeadFormActivity, { LeadFormActivityProps, LeadFormActivityState } from './LeadFormActivity';
import Hyperlink from '@hubleto/react-ui/core/Inputs/Hyperlink';
import { FormProps, FormState } from '@hubleto/react-ui/core/Form';
import moment, { Moment } from "moment";
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';

import TableLeadHistory from './TableLeadHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';

export interface FormLeadProps extends HubletoFormProps {
  newEntryId?: number,
}

export interface FormLeadState extends HubletoFormState {
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  tableLeadDocumentsDescription: any,
  tablesKey: number,
}

export default class FormLead<P, S> extends HubletoForm<FormLeadProps,FormLeadState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Leads/Models/Lead',
  };

  props: FormLeadProps;
  state: FormLeadState;

  refLogActivityInput: any;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

  parentApp: string = 'HubletoApp/Community/Leads';

  constructor(props: FormLeadProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdDocument: 0,
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      tableLeadDocumentsDescription: null,
      tablesKey: 0,
    };
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormLeadState)
  }

  getStateFromProps(props: FormLeadProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Lead')}</b> },
        { uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'calendar', icon: 'fas fa-calendar', position: 'right' },
        { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  getFormHeaderButtons()
  {
    return [
      ...super.getFormHeaderButtons(),
      {
        title: 'Close lead',
        onClick: () => { }
      }
    ]
  }

  getRecordFormUrl(): string {
    return 'leads/' + this.state.record.id;
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Leads/Models/LeadDocument',
        idLead: this.state.id,
      },
      (description: any) => {
        this.setState({tableLeadDocumentsDescription: description} as any);
      }
    );

    return description;
  }

  onAfterSaveRecord(saveResponse: any): void {
    let params = this.getEndpointParams() as any;
    let isArchived = saveResponse.savedRecord.is_archived;

    if (params.showArchive == false && isArchived == true) {
      this.props.onClose();
      this.props.parentTable.loadData();
    }
    else if (params.showArchive == true && isArchived == false) {
      this.props.onClose();
      this.props.parentTable.loadData();
    } else super.onAfterSaveRecord(saveResponse);
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            this.updateRecord({id_pipeline: idPipeline, id_pipeline_step: idPipelineStep});
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            this.updateRecord({id_pipeline_step: idPipelineStep});
          }}
        ></PipelineSelector>
        {this.inputWrapper('is_closed')}
      </>}
    </>
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    
    let values = [];
    if (R && R.CONTACT) {
      if (R.CONTACT.first_name) values.push(R.CONTACT.first_name);
      if (R.CONTACT.last_name) values.push(R.CONTACT.last_name);
      if (R.CONTACT.VALUES) R.CONTACT.VALUES.map((item, key) => {
        values.push(item.value);
      });
    }

    return <>
      <small>Lead</small>
      <h2>{values && values.length > 0 ? values.join(', ') : '-'}</h2>
    </>;
  }

  renderSubTitle(): JSX.Element {
    return <small>{this.translate('Lead')}</small>;
  }

  moveToArchive(recordId: number) {
    request.get(
      'leads/api/move-to-archive',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          this.props.parentTable.setState({recordId: null}, () => {
            this.props.parentTable.loadData();
          });
        }
      }
    );
  }

  logCompletedActivity() {
    request.get(
      'leads/api/log-activity',
      {
        idLead: this.state.record.id,
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
    } as FormLeadState);
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        //@ts-ignore
        const tmpCalendarSmall = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=leads&idLead=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormLeadState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormLeadState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            {tmpCalendarSmall}
          </div>
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
                onClick={() => this.setState({showIdActivity: item.id} as FormLeadState)}
              >
                <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
                <span className="text">
                  {item.subject}
                  {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
                </span>
              </button>
            </>
          })}</div> : null}
        </div>;

        return <>
          {R.is_archived == 1 ?
            <div className='alert-warning mt-2 mb-1'>
              <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
              <span className='text'>{this.translate("This lead is archived.")}</span>
            </div>
          : null}
          <div className='flex gap-2 mt-2'>
            <div className='flex-2'>
              <div className='card card-body flex flex-row gap-2'>
                <div className='grow'>
                  <FormInput title={"Campaign"}>
                    {R.CAMPAIGNS ? R.CAMPAIGNS.map((item, key) => {
                      return <a
                        key={key}
                        className='badge'
                        href={globalThis.main.config.projectUrl + '/campaigns/' + item.CAMPAIGN.id}
                        target='_blank'
                      >{item.CAMPAIGN.name}</a>;
                    }) : null}
                  </FormInput>
                  {/* {this.inputWrapper('identifier', {readonly: R.is_archived})} */}
                  <FormInput title={"Contact"} required={true}>
                    <Lookup {...this.getInputProps('id_contact')}
                      model='HubletoApp/Community/Contacts/Models/Contact'
                      customEndpointParams={{idCustomer: R.id_customer}}
                      readonly={R.is_archived}
                      value={R.id_contact}
                      urlAdd='contacts/add'
                      onChange={(input: any, value: any) => {
                        this.updateRecord({ id_contact: value })
                        if (R.id_contact == 0) {
                          R.id_contact = null;
                          this.setState({record: R})
                        }
                      }}
                    ></Lookup>
                  </FormInput>
                  {R.CONTACT && R.CONTACT.VALUES ? <div className="ml-4 text-sm p-2 bg-lime-100 text-lime-900 mb-2">
                    {R.CONTACT.VALUES.map((item, key) => {
                      return <div key={key}>{item.value}</div>;
                    })}
                  </div> : null}
                  {this.inputWrapper('title', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
                  {/* {this.inputWrapper('id_level', {readonly: R.is_archived, uiStyle: 'buttons'})}
                  {this.inputWrapper('status', {readonly: R.is_archived, uiStyle: 'buttons', onChange: (input: any, value: any) => {this.updateRecord({lost_reason: null})}})} */}
                  {this.inputWrapper('note', {cssClass: 'bg-yellow-50', readonly: R.is_archived})}
                  {this.state.record.status == 4 ? this.inputWrapper('lost_reason', {readonly: R.is_archived}): null}
                </div>
                <div className='border-l border-gray-200'></div>
                <div className='grow'>
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price', { cssClass: 'text-2xl', readonly: R.is_archived ? true : false })}
                    {this.inputWrapper('id_currency')}
                  </div>
                  {this.inputWrapper('score', {readonly: R.is_archived})}
                  {this.inputWrapper('id_owner', {readonly: R.is_archived})}
                  {this.inputWrapper('id_manager', {readonly: R.is_archived})}
                  {this.inputWrapper('id_team', {readonly: R.is_archived})}
                  {this.inputWrapper('date_expected_close', {readonly: R.is_archived})}
                  {this.inputWrapper('source_channel', {readonly: R.is_archived})}
                  <FormInput title='Tags'>
                    <InputTags2 {...this.getInputProps('tags_input')}
                      value={this.state.record.TAGS}
                      readonly={R.is_archived}
                      model='HubletoApp/Community/Leads/Models/Tag'
                      targetColumn='id_lead'
                      sourceColumn='id_tag'
                      colorColumn='color'
                      onChange={(input: any, value: any) => {
                        R.TAGS = value;
                        this.setState({record: R});
                      }}
                    ></InputTags2>
                  </FormInput>
                  <FormInput title={"Customer"}>
                    <Lookup {...this.getInputProps('id_customer')}
                      model='HubletoApp/Community/Customers/Models/Customer'
                      urlAdd='customers/add'
                      readonly={R.is_archived}
                      value={R.id_customer}
                      onChange={(input: any, value: any) => {
                        this.updateRecord({ id_customer: value, id_contact: null });
                        if (R.id_customer == 0) {
                          R.id_customer = null;
                          this.setState({record: R});
                        }
                      }}
                    ></Lookup>
                  </FormInput>
                  {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
                  {this.inputWrapper('date_created')}
                  {this.inputWrapper('is_archived')}
                </div>
              </div>
            </div>
            {this.state.id > 0 ? recentActivitiesAndCalendar : null}
          </div>
        </>
      break;

      case 'calendar':
        //@ts-ignore
        const tmpCalendarLarge = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='timeGridWeek'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=leads&idLead=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormLeadState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormLeadState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;
        return <>
          {tmpCalendarLarge}
          {this.state.showIdActivity == 0 ? <></> :
            <ModalForm
              uid='activity_form'
              isOpen={true}
              type='right'
            >
              <LeadFormActivity
                id={this.state.showIdActivity}
                isInlineEditing={true}
                description={{
                  defaultValues: {
                    id_lead: R.id,
                    id_contact: R.id_contact,
                    date_start: this.state.activityDate,
                    time_start: this.state.activityTime == "00:00:00" ? null : this.state.activityTime,
                    date_end: this.state.activityDate,
                    all_day: this.state.activityAllDay,
                    subject: this.state.activitySubject,
                  }
                }}
                idCustomer={R.id_customer}
                showInModal={true}
                showInModalSimple={true}
                onClose={() => { this.setState({ showIdActivity: 0 } as FormLeadState) }}
                onSaveCallback={(form: LeadFormActivity<LeadFormActivityProps, LeadFormActivityState>, saveResponse: any) => {
                  if (saveResponse.status == "success") {
                    this.setState({ showIdActivity: 0 } as FormLeadState);
                  }
                }}
              ></LeadFormActivity>
            </ModalForm>
          }
        </>
      break;

      case 'documents':
        return <TableDocuments
          tag={"table_lead_document"}
          parentForm={this}
          uid={this.props.uid + "_table_lead_document"}
          junctionTitle='Lead'
          junctionModel='HubletoApp/Community/Leads/Models/LeadDocument'
          junctionSourceColumn='id_lead'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_document'
        />;
      break;

      case 'tasks':
        return <TableTasks
          tag={"table_lead_task"}
          parentForm={this}
          uid={this.props.uid + "_table_lead_task"}
          junctionTitle='Lead'
          junctionModel='HubletoApp/Community/Leads/Models/LeadTask'
          junctionSourceColumn='id_lead'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_task'
        />;
      break;

      case 'history':

        if (R.HISTORY && R.HISTORY.length > 0) {
          if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
            R.HISTORY = this.state.record.HISTORY.reverse();
        }

        return <>
          <div className='card'>
            <div className='card-body [&_*]:whitespace-normal'>
              <TableLeadHistory
                uid={this.props.uid + "_table_lead_history"}
                data={{ data: R.HISTORY }}
                descriptionSource="props"
                onRowClick={(table) => {}}
                description={{
                  permissions: {
                    canCreate: false,
                    canDelete: false,
                    canRead: true,
                    canUpdate: false,
                  },
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  columns: {
                    description: { type: "varchar", title: this.translate("Description")},
                    change_date: { type: "date", title: this.translate("Change Date")},
                  },
                  inputs: {
                    description: { type: "varchar", title: this.translate("Description"), readonly: true},
                    change_date: { type: "date", title: this.translate("Change Date")},
                  },
                }}
                readonly={true}
              ></TableLeadHistory>
            </div>
          </div>
        </>;
      break;

      default:
        super.renderTab(tab);
      break;
    }
  }
}