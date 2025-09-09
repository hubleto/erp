import React, { Component, createRef, ChangeEvent } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';
import moment, { Moment } from "moment";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

import Calendar from '../../Calendar/Components/Calendar';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import DealFormActivity, { DealFormActivityProps, DealFormActivityState } from './DealFormActivity';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import Hyperlink from '@hubleto/react-ui/core/Inputs/Hyperlink';
import { FormProps, FormState } from '@hubleto/react-ui/core/Form';
import TableDealProducts from './TableDealProducts';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TableDealHistory from './TableDealHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableOrders from '@hubleto/apps/Orders/Components/TableOrders';
// import TableProjects from '@hubleto/apps/Projects/Components/TableProjects';
import WorkflowSelector from '../../Workflow/Components/WorkflowSelector';

export interface FormDealProps extends HubletoFormProps {
  newEntryId?: number,
}

export interface FormDealState extends HubletoFormState {
  newEntryId?: number,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  tableDealProductsDescription: any,
  tableDealDocumentsDescription: any,
  tablesKey: number,
  // workflowFirstLoad: boolean;
}

export default class FormDeal<P, S> extends HubletoForm<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Deals/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  parentApp: string = 'Hubleto/App/Community/Deals';

  refLogActivityInput: any;
  refProductsLookup: any;

  translationContext: string = 'Hubleto\\App\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: FormDealProps) {
    super(props);

    this.refLogActivityInput = React.createRef();
    this.refProductsLookup = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      tableDealProductsDescription: null,
      tableDealDocumentsDescription: null,
      tablesKey: 0,
      // workflowFirstLoad: false,
    };
    this.onCreateActivityCallback = this.onCreateActivityCallback.bind(this);
  }

  getStateFromProps(props: FormDealProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Deal')}</b> },
        { uid: 'products', title: this.translate('Products'), showCountFor: 'PRODUCTS' },
        { uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'calendar', icon: 'fas fa-calendar', position: 'right' },
        { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ],
    };
  }

  getRecordFormUrl(): string {
    return 'deals/' + this.state.record.id;
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Deals/Models/DealProduct',
        idDeal: this.state.id,
      },
      (description: any) => {
        this.setState({tableDealProductsDescription: description} as any);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Deals/Models/DealDocument',
        idDeal: this.state.id,
      },
      (description: any) => {
        this.setState({tableDealDocumentsDescription: description} as any);
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

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Deal")}</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderSubTitle(): JSX.Element {
    return <small>{this.translate('Deal')}</small>;
  }

  // workflowChange(idWorkflow: number) {
  //   request.get(
  //     'deals/change-workflow',
  //     {
  //       idWorkflow: idWorkflow
  //     },
  //     (data: any) => {
  //       if (data.status == "success") {
  //         var R = this.state.record;
  //         if (data.newWorkflow.STEPS?.length > 0) {
  //           R.id_workflow = data.newWorkflow.id;
  //           R.id_workflow_step = data.newWorkflow.STEPS[0].id;
  //           R.deal_result = data.newWorkflow.STEPS[0].set_result;
  //           R.WORKFLOW = data.newWorkflow;
  //           R.WORKFLOW_STEP = data.newWorkflow.STEPS[0];

  //           this.setState({ record: R });
  //         } else {
  //           R.id_workflow = data.newWorkflow.id;
  //           R.id_workflow_step = null;
  //           R.WORKFLOW = data.newWorkflow;
  //           R.WORKFLOW_STEP = null;

  //           this.setState({ record: R });
  //         }
  //       }
  //     }
  //   );
  // }

  calculateWeightedProfit(probability: number, price: number) {
    return (probability / 100) * price;
  }

  // changeWorkflowStepFromResult() {
  //   if (this.state.record.WORKFLOW.STEPS.length > 0) {
  //     this.state.record.WORKFLOW.STEPS.some(step => {
  //       if (step.set_result == this.state.record.deal_result) {
  //         let R = this.state.record;
  //         R.id_workflow_step = step.id;
  //         R.WORKFLOW_STEP = step;
  //         this.setState({record: R});
  //         return true;
  //       } else return false;
  //     })
  //   }
  // }

  onCreateActivityCallback() {
    this.loadRecord();
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormDealState)
  }

  logCompletedActivity() {
    request.get(
      'deals/api/log-activity',
      {
        idDeal: this.state.record.id,
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
    } as FormDealState);
  }

  getFormHeaderButtons()
  {
    return [
      ...super.getFormHeaderButtons(),
      {
        title: 'Generate quotation (PDF)',
        onClick: () => {
          request.post(
            'deals/api/generate-quotation-pdf',
            {idDeal: this.state.record.id},
            {},
            (result: any) => {
              if (result.idDocument) {
                window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
              }
            }
          );
        }
      },
      {
        title: 'Close deal',
        onClick: () => { }
      }
    ]
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':

        const inputsColumnLeft = <>
          <FormInput title={"Lead"}>
            {R.LEADS ? R.LEADS.map((item, key) => {
              return (item.LEAD ? <a
                key={key}
                className='badge'
                href={globalThis.main.config.projectUrl + '/leads/' + item.LEAD.id}
                target='_blank'
              >{item.LEAD.id}</a> : '#');
            }) : null}
          </FormInput>
          {this.inputWrapper('identifier', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
          {this.inputWrapper('title', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
          {this.inputWrapper('version')}
          <FormInput title={"Customer"}>
            <Lookup {...this.getInputProps("id_customer")}
              model='Hubleto/App/Community/Customers/Models/Customer'
              urlAdd='customers/add'
              value={R.id_customer}
              readonly={R.is_archived}
              onChange={(input: any, value: any) => {
                this.updateRecord({ id_customer: value, id_contact: null });
                if (R.id_customer == 0) {
                  R.id_customer = null;
                  this.setState({record: R});
                }
              }}
            ></Lookup>
          </FormInput>
          <FormInput title={"Contact"}>
            <Lookup {...this.getInputProps("id_contact")}
              model='Hubleto/App/Community/Contacts/Models/Contact'
              customEndpointParams={{idCustomer: R.id_customer}}
              value={R.id_contact}
              urlAdd='contacts/add'
              readonly={R.is_archived}
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
          <div className='flex flex-row *:w-1/2'>
            {this.inputWrapper('price_excl_vat', {
              cssClass: 'text-2xl',
              readonly: (R.PRODUCTS && R.PRODUCTS.length > 0) || R.is_archived ? true : false,
            })}
            {this.inputWrapper('id_currency')}
          </div>
          <div className='flex flex-row *:w-1/2'>
            {this.inputWrapper('price_incl_vat', {
              readonly: (R.PRODUCTS && R.PRODUCTS.length > 0) || R.is_archived ? true : false,
            })}
            {this.state.isInlineEditing && (R.PRODUCTS && R.PRODUCTS.length > 0) ?
              <div className='badge badge-warning'>
                <span className='icon mr-2'><i className='fas fa-warning'></i></span>
                <span className='text'>Price cannot be changed, it is calculated from product prices.</span>
              </div>
            : <></>}
          </div>
          {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
          {this.inputWrapper('customer_order_number', {readonly: R.is_archived})}
          {this.inputWrapper('id_template_quotation', {readonly: R.is_archived})}
        </>;

        const inputsColumnRight = <>
          {this.inputWrapper('id_owner', {readonly: R.is_archived})}
          {this.inputWrapper('id_manager', {readonly: R.is_archived})}
          {this.inputWrapper('date_expected_close', {readonly: R.is_archived})}
          <div className="flex gap-2">
            {this.inputWrapper('source_channel', {readonly: R.is_archived})}
            {this.inputWrapper('is_new_customer', {readonly: R.is_archived, onChange: (input: any, value: any) => {
              if (this.state.record.is_new_customer) {
                this.updateRecord({business_type: 1 /* New */});
              }
            }})}
          </div>
          <div className="flex gap-2">
            {this.inputWrapper('business_type', {uiStyle: 'buttons', readonly: R.is_archived, onChange: (input: any, value: any) => {
              if (this.state.record.business_type == 2 /* Existing */) {
                this.updateRecord({is_new_customer: false});
              }
            }})}
            {this.inputWrapper("deal_result",
              {
                uiStyle: 'buttons',
                readonly: R.is_archived,
                onChange: (input: any, value: any) => {
                  this.updateRecord({lost_reason: null});
                  // if (this.state.record.WORKFLOW && this.state.record.WORKFLOW.STEPS?.length > 0) {
                  //   this.changeWorkflowStepFromResult();
                  // }
                }
              }
            )}
          </div>
          {this.inputWrapper('date_created')}
          {this.inputWrapper('note', {cssClass: 'bg-yellow-50', readonly: R.is_archived})}
          {this.state.record.deal_result == 2 ? this.inputWrapper('lost_reason', {readonly: R.is_archived}): null}
        </>;

        //@ts-ignore
        const tmpCalendar = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=deals&idDeal=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormDealState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormDealState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            {tmpCalendar}
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
            return <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
              onClick={() => this.setState({showIdActivity: item.id} as FormDealState)}
            >
              <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
              <span className="text">
                {item.subject}
                {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
              </span>
            </button>;
          })}</div> : null}
        </div>;


        return <>
          {R.is_archived == 1 ?
            <div className='alert-warning mt-2 mb-1'>
              <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
              <span className='text'>{this.translate("This deal is archived")}</span>
            </div>
          : null}
          <div className='flex gap-2 flex-col md:flex-row'>
            <div className='flex-2'>
              <div className='card card-body flex flex-col md:flex-row gap-2'>
                <div className='grow'>{inputsColumnLeft}</div>
                <div className='border-t md:border-l border-gray-200'></div>
                <div className='grow'>{inputsColumnRight}</div>
              </div>
            </div>
            <div className='flex-1'>
              {this.state.id > 0 ? <div className='flex gap-2 mb-2'>
                <div className="badge badge-violet">
                  {this.translate("Deal value:")} {globalThis.main.numberFormat(R.price_excl_vat, 2, ",", " ")} {R.CURRENCY.code}
                </div>
                {R.WORKFLOW_STEP && R.WORKFLOW_STEP.probability ?
                  <div className="badge badge-violet">
                    {this.translate("Weighted profit")} ({R.WORKFLOW_STEP?.probability} %):
                    <strong> {globalThis.main.numberFormat(this.calculateWeightedProfit(R.WORKFLOW_STEP?.probability, R.price_excl_vat), 2, ',', ' ')} {R.CURRENCY.code}</strong>
                  </div>
                : null}
              </div> : null}
              {this.state.id > 0 ? recentActivitiesAndCalendar : null}
            </div>
          </div>
        </>
      break;

      case 'products':

        var lookupData;

        const getLookupData = (lookupElement) => {
          if (lookupElement.current) {
            lookupData = lookupElement.current.state.data;
          }
        }

        return <>
          <div className='w-full h-full overflow-x-auto'>
            <TableDealProducts
              key={"products_"+this.state.tablesKey}
              uid={this.props.uid + "_table_deal_products"}
              tag={"deal_products"}
              parentForm={this}
              idDeal={R.id}
              descriptionSource='both'
              readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
            ></TableDealProducts>
          </div>
        </>;

      break;

      case 'calendar':
        //@ts-ignore
        return <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='timeGridWeek'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=deals&idDeal=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormDealState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormDealState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;
      break;

      case 'tasks':
        return <TableTasks
          tag={"table_deal_task"}
          parentForm={this}
          uid={this.props.uid + "_table_deal_task"}
          junctionTitle='Deal'
          junctionModel='Hubleto/App/Community/Deals/Models/DealTask'
          junctionSourceColumn='id_deal'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_task'
        />;
      break;

      case 'documents':
        return <>
          <TableDocuments
            key={this.state.tablesKey + "_table_deal_document"}
            uid={this.props.uid + "_table_deal_documents"}
            tag={'table_deal_documents'}
            parentForm={this}
            junctionModel='Hubleto\App\Community\Deals\Models\DealDocument'
            junctionSourceColumn='id_deal'
            junctionDestinationColumn='id_document'
            junctionSourceRecordId={R.id}
            readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
          />
        </>
      break;

      case 'history':
        if (R.HISTORY && R.HISTORY.length > 0) {
          if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
            R.HISTORY = this.state.record.HISTORY.reverse();
        }

        return <>
          <div className='card'>
            <div className='card-body [&_*]:whitespace-normal'>
              <TableDealHistory
                uid={this.props.uid + "_table_deal_history"}
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
              ></TableDealHistory>
            </div>
          </div>
        </>
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
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
            let newRecord: any = {id_workflow_step: idWorkflowStep, deal_result: 0, is_closed: false};
            if (step.name.match(/won/i)) newRecord.deal_result = 1;
            if (step.name.match(/lost/i)) newRecord.deal_result = 2;
            if (newRecord.deal_result != 0) newRecord.is_closed = true;
            this.updateRecord(newRecord);
          }}
        ></WorkflowSelector>
        {this.inputWrapper('is_closed', {readonly: R.is_archived})}
      </>}
    </>
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderContent()}
      {this.state.showIdActivity == 0 ? <></> :
        <ModalForm
          uid='activity_form'
          isOpen={true}
          type='right'
        >
          <DealFormActivity
            id={this.state.showIdActivity}
            isInlineEditing={true}
            description={{
              defaultValues: {
                id_deal: R.id,
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
            onClose={() => { this.setState({ showIdActivity: 0 } as FormDealState) }}
            onSaveCallback={(form: DealFormActivity<DealFormActivityProps, DealFormActivityState>, saveResponse: any) => {
              if (saveResponse.status == "success") {
                this.setState({ showIdActivity: 0 } as FormDealState);
              }
            }}
          ></DealFormActivity>
        </ModalForm>
      }
    </>;
  }
}