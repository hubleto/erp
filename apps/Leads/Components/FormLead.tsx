import React, { Component, createRef, ChangeEvent } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import InputTags2 from '@hubleto/react-ui/core/Inputs/Tags2';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';
import { TabPanel, TabView } from 'primereact/tabview';
import Calendar from '../../Calendar/Components/Calendar';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import TableLeadDocuments from './TableLeadDocuments';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import LeadFormActivity, { LeadFormActivityProps, LeadFormActivityState } from './LeadFormActivity';
import Hyperlink from '@hubleto/react-ui/core/Inputs/Hyperlink';
import { FormProps, FormState } from '@hubleto/react-ui/core/Form';
import moment, { Moment } from "moment";
import TableLeadHistory from './TableLeadHistory';

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
    tabs: {
      'default': { title: 'Lead' },
      'calendar': { title: 'Calendar' },
      'documents': { title: 'Documents' },
      'history': { title: 'History' },
    }
  };

  props: FormLeadProps;
  state: FormLeadState;

  refLogActivityInput: any;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

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
    };
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

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ? this.state.record.title : '-'}</h2>
      <small>Lead</small>
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

  moveToArchiveConfirm(recordId: number) {
    globalThis.main.showDialogConfirm(
      <div>{this.translate("Are you sure you want to move this lead to archive?")}</div>,
      {
        header: this.translate("Move to archive"),
        yesText: this.translate("Yes, move to archive"),
        onYes: () => this.moveToArchive(recordId),
        noText: this.translate("No, do not move to archive"),
        onNo: () => {},
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

  renderTopMenu(): null|JSX.Element {
    return <>
      {super.renderTopMenu()}
      {this.state.record.is_archived ? null :
        <a className='btn btn-transparent' onClick={() => this.moveToArchiveConfirm(this.state.record.id)}>
          <span className='icon'><i className='fas fa-box-archive'></i></span>
          <span className='text'>Move to archive</span>
        </a>
      }
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            <Calendar
              onCreateCallback={() => this.loadRecord()}
              readonly={R.is_archived}
              initialView='dayGridMonth'
              headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
              eventsEndpoint={globalThis.main.config.rootUrl + '/calendar/api/get-calendar-events?source=leads&idLead=' + R.id}
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
            ></Calendar>
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
                  <div className='flex gap-2'>
                    <div className='w-full'>
                      {this.inputWrapper('id_campaign', {readonly: R.is_archived})}
                    </div>
                    <div className='w-full'>
                      {this.inputWrapper('identifier', {readonly: R.is_archived})}
                    </div>
                  </div>
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
                  {this.inputWrapper('id_level', {readonly: R.is_archived, uiStyle: 'buttons'})}
                  {this.inputWrapper('status', {readonly: R.is_archived, uiStyle: 'buttons', onChange: (input: any, value: any) => {this.updateRecord({lost_reason: null})}})}
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
        return <>
          <Calendar
            onCreateCallback={() => this.loadRecord()}
            readonly={R.is_archived}
            initialView='timeGridWeek'
            views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
            eventsEndpoint={globalThis.main.config.rootUrl + '/calendar/api/get-calendar-events?source=leads&idLead=' + R.id}
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
          ></Calendar>
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
        return <>
          <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
          {!R.is_archived ?
            <a
              className="btn btn-add-outline mb-2"
              onClick={() => this.setState({showIdDocument: -1} as FormLeadState)}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">{this.translate("Add document")}</span>
            </a>
          : null}
          <TableLeadDocuments
            key={this.state.tablesKey + "_table_lead_document"}
            uid={this.props.uid + "_table_lead_document"}
            data={{ data: R.DOCUMENTS }}
            descriptionSource="both"
            customEndpointParams={{idLead: R.id}}
            description={{
              permissions: this.state.tableLeadDocumentsDescription?.permissions,
              ui: {
                showFooter: false,
                showHeader: false,
              },
              columns: {
                id_document: { type: "lookup", title: this.translate("Document"), model: "HubletoApp/Community/Documents/Models/Document" },
                hyperlink: { type: "varchar", title: this.translate("Link"), cellRenderer: ( table: TableLeadDocuments, data: any, options: any): JSX.Element => {
                  return (
                    <FormInput>
                      <Hyperlink {...this.getInputProps('link_input')}
                        value={data.DOCUMENT.hyperlink}
                        readonly={true}
                      ></Hyperlink>
                    </FormInput>
                  )
                }},
              },
              inputs: {
                id_document: { type: "lookup", title: this.translate("Document"), model: "HubletoApp/Community/Documents/Models/Document" },
                hyperlink: { type: "varchar", title: this.translate("Link"), readonly: true},
              },
            }}
            isUsedAsInput={true}
            readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
            onRowClick={(table: TableLeadDocuments, row: any) => {
              this.setState({showIdDocument: row.id_document} as FormLeadState);
            }}
            onDeleteSelectionChange={(table) => {
              this.updateRecord({ DOCUMENTS: table.state.data?.data ?? []});
              this.setState({tablesKey: Math.random()} as FormLeadState)
            }}
          />
          {this.state.showIdDocument != 0 ?
            <ModalForm
              uid='document_form'
              isOpen={true}
              type='right'
            >
              <FormDocument
                id={this.state.showIdDocument}
                onClose={() => this.setState({showIdDocument: 0} as FormLeadState)}
                showInModal={true}
                descriptionSource="both"
                description={{
                  defaultValues: {
                    creatingForModel: "HubletoApp/Community/Leads/Models/LeadDocument",
                    creatingForId: this.state.record.id,
                    origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
                  }
                }}
                isInlineEditing={this.state.showIdDocument < 0 ? true : false}
                showInModalSimple={true}
                onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                  if (saveResponse.status = "success") {
                    this.loadRecord();
                    this.setState({ showIdDocument: 0 } as FormLeadState)
                  }
                }}
                onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                  if (saveResponse.status = "success") {
                    this.loadRecord();
                    this.setState({ showIdDocument: 0 } as FormLeadState)
                  }
                }}
              />
            </ModalForm>
          : null}
        </>;
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

    }
  }

  // renderContent(): JSX.Element {
  //   const R = this.state.record;
  //   const showAdditional = R.id > 0 ? true : false;


  //   // if (R.DEAL) R.DEAL.checkOwnership = false;

  //   return (
  //     <>
  //       {this.state.id > 0 ?
  //         <div className="h-0 w-full text-right">
  //           <div className="badge badge-secondary badge-large">
  //             Lead value:&nbsp;{globalThis.main.numberFormat(R.price ?? 0, 2, ",", " ")} {R.CURRENCY.code}
  //           </div>
  //         </div>
  //       : null}
  //       <TabView>
  //         {showAdditional ?
  //         : null}
  //       </TabView>
  //     </>
  //   );
  // }
}