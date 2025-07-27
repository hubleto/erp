import React, { Component, createRef, ChangeEvent } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';
import TableDealProducts from './TableDealProducts';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { TabPanel, TabView } from 'primereact/tabview';
import Calendar from '../../Calendar/Components/Calendar';
import TableDealDocuments from './TableDealDocuments';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import DealFormActivity, { DealFormActivityProps, DealFormActivityState } from './DealFormActivity';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import Hyperlink from '@hubleto/react-ui/core/Inputs/Hyperlink';
import { FormProps, FormState } from '@hubleto/react-ui/core/Form';
import moment, { Moment } from "moment";
import TableDealHistory from './TableDealHistory';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';

export interface FormDealProps extends HubletoFormProps {
  newEntryId?: number,
}

export interface FormDealState extends HubletoFormState {
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  tableDealProductsDescription: any,
  tableDealDocumentsDescription: any,
  tablesKey: number,
  // pipelineFirstLoad: boolean;
}

export default class FormDeal<P, S> extends HubletoForm<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Deals/Models/Deal',
    tabs: {
      'default': { title: 'Deal' },
      'items': { title: 'Items' },
      'calendar': { title: 'Calendar' },
      'documents': { title: 'Documents' },
      'history': { title: 'History' },
    }
  };

  props: FormDealProps;
  state: FormDealState;

  refLogActivityInput: any;
  refServicesLookup: any;
  refProductsLookup: any;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: FormDealProps) {
    super(props);

    this.refLogActivityInput = React.createRef();
    this.refServicesLookup = React.createRef();
    this.refProductsLookup = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdDocument: 0,
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      tableDealProductsDescription: null,
      tableDealDocumentsDescription: null,
      tablesKey: 0,
      // pipelineFirstLoad: false,
    };
    this.onCreateActivityCallback = this.onCreateActivityCallback.bind(this);
  }

  getStateFromProps(props: FormDealProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Deals/Models/DealProduct',
        idDeal: this.state.id,
      },
      (description: any) => {
        this.setState({tableDealProductsDescription: description} as any);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Deals/Models/DealDocument',
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

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ? this.state.record.title : '-'}</h2>
      <small>{this.translate("Deal")}</small>
    </>;
  }

  renderSubTitle(): JSX.Element {
    return <small>{this.translate('Deal')}</small>;
  }

  pipelineChange(idPipeline: number) {
    request.get(
      'deals/change-pipeline',
      {
        idPipeline: idPipeline
      },
      (data: any) => {
        if (data.status == "success") {
          var R = this.state.record;
          if (data.newPipeline.STEPS?.length > 0) {
            R.id_pipeline = data.newPipeline.id;
            R.id_pipeline_step = data.newPipeline.STEPS[0].id;
            R.deal_result = data.newPipeline.STEPS[0].set_result;
            R.PIPELINE = data.newPipeline;
            R.PIPELINE_STEP = data.newPipeline.STEPS[0];

            this.setState({ record: R });
          } else {
            R.id_pipeline = data.newPipeline.id;
            R.id_pipeline_step = null;
            R.PIPELINE = data.newPipeline;
            R.PIPELINE_STEP = null;

            this.setState({ record: R });
          }
        }
      }
    );
  }

  calculateWeightedProfit(probability: number, price: number) {
    return (probability / 100) * price;
  }

  changePipelineStepFromResult() {
    if (this.state.record.PIPELINE.STEPS.length > 0) {
      this.state.record.PIPELINE.STEPS.some(step => {
        if (step.set_result == this.state.record.deal_result) {
          let R = this.state.record;
          R.id_pipeline_step = step.id;
          R.PIPELINE_STEP = step;
          this.setState({record: R});
          return true;
        } else return false;
      })
    }
  }

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

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':

        const inputsColumnLeft = <>
          {this.inputWrapper('identifier', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
          {this.inputWrapper('title', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
          <FormInput title={"Customer"}>
            <Lookup {...this.getInputProps("id_customer")}
              model='HubletoApp/Community/Customers/Models/Customer'
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
              model='HubletoApp/Community/Contacts/Models/Contact'
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
          {this.state.isInlineEditing && (R.PRODUCTS && R.PRODUCTS.length > 0) || (R.SERVICES && R.SERVICES.length > 0) ?
            <div className='text-yellow-500 mb-3'>
              <span className='icon mr-2'><i className='fas fa-warning'></i></span>
              <span className='text'>The price is locked because the deal has some products or services</span>
            </div>
          : <></>}
          <div className='flex flex-row *:w-1/2'>
            {this.inputWrapper('price', {
              cssClass: 'text-2xl',
              readonly: (R.PRODUCTS && R.PRODUCTS.length > 0) || (R.SERVICES && R.SERVICES.length > 0) || R.is_archived ? true : false,
            })}
            {this.inputWrapper('id_currency')}
          </div>
          {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
          {this.inputWrapper('customer_order_number', {readonly: R.is_archived})}
          {this.inputWrapper('is_closed', {readonly: R.is_archived})}
        </>;

        const inputsColumnRight = <>
          <div className="flex gap-2">
            {this.inputWrapper('id_owner', {readonly: R.is_archived})}
            {this.inputWrapper('id_manager', {readonly: R.is_archived})}
          </div>
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
                  if (this.state.record.PIPELINE && this.state.record.PIPELINE.STEPS?.length > 0) {
                    this.changePipelineStepFromResult();
                  }
                }
              }
            )}
          </div>
          {this.inputWrapper('date_created')}
          {this.inputWrapper('is_archived')}
          {this.inputWrapper('id_lead')}
          {this.inputWrapper('note', {cssClass: 'bg-yellow-50', readonly: R.is_archived})}
          {this.state.record.deal_result == 3 ? this.inputWrapper('lost_reason', {readonly: R.is_archived}): null}
        </>;

        const pipeline = <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            this.pipelineChange(idPipeline);
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            if (!R.is_archived) {
              if (this.state.isInlineEditing == false) this.setState({isInlineEditing: true});
              R.id_pipeline_step = idPipelineStep;
              R.deal_result = step.set_result;
              R.PIPELINE_STEP = step;
              this.updateRecord(R);
            }
          }}
        ></PipelineSelector>;

        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            <Calendar
              onCreateCallback={() => this.loadRecord()}
              readonly={R.is_archived}
              initialView='dayGridMonth'
              headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
              eventsEndpoint={globalThis.main.config.rootUrl + '/calendar/api/get-calendar-events?source=deals&idDeal=' + R.id}
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
              {pipeline}
            </div>
            <div className='flex-1'>
              {this.state.id > 0 ? <div className='flex flex-col'>
                <div className="badge badge-violet badge-large">
                  {this.translate("Deal value:")} &nbsp; {globalThis.main.numberFormat(R.price, 2, ",", " ")} {R.CURRENCY.code}
                </div>
                {R.PIPELINE_STEP && R.PIPELINE_STEP.probability ?
                  <div className="badge badge-violet badge-large">
                    <p>{this.translate("Weighted profit")} ({R.PIPELINE_STEP?.probability} %):
                      <strong> {globalThis.main.numberFormat(this.calculateWeightedProfit(R.PIPELINE_STEP?.probability, R.price), 2, ',', ' ')} {R.CURRENCY.code}</strong>
                    </p>
                  </div>
                : null}
              </div> : null}
              {this.state.id > 0 ? recentActivitiesAndCalendar : null}
            </div>
          </div>
        </>
      break;

      case 'items':

        var lookupData;

        const getLookupData = (lookupElement) => {
          if (lookupElement.current) {
            lookupData = lookupElement.current.state.data;
          }
        }

        return <>
          <div className='flex gap-2 mt-2'>
            <div className='card flex-2'>
              <div className='card-header'>{this.translate("Products & Services")}</div>
              <div className='card-body'>
                {this.state.isInlineEditing ?
                  <div className='text-yellow-500 mb-3'>
                    <span className='icon mr-2'><i className='fas fa-warning'></i></span>
                    <span className='text'>{this.translate("The sums of product and services prices will be updated after saving")}</span>
                  </div>
                : <></>}
                {!R.is_archived ? (
                  <a
                    className="btn btn-add-outline mb-2 mr-2"
                    onClick={() => {
                      if (!R.SERVICES) R.SERVICES = [];
                      R.SERVICES.push({
                        id: this.state.newEntryId,
                        id_deal: { _useMasterRecordId_: true },
                        amount: 1,
                      });
                      this.setState({ isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormDealState);
                    }}
                  >
                    <span className="icon"><i className="fas fa-add"></i></span>
                    <span className="text">{this.translate("Add service")}</span>
                  </a>
                ) : null}
                {!R.is_archived ? (
                  <a
                    className="btn btn-add-outline mb-2"
                    onClick={() => {
                      if (!R.PRODUCTS) R.PRODUCTS = [];
                      R.PRODUCTS.push({
                        id: this.state.newEntryId,
                        id_deal: { _useMasterRecordId_: true },
                        amount: 1,
                      });
                      this.setState({ isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormDealState);
                    }}
                  >
                    <span className="icon"><i className="fas fa-add"></i></span>
                    <span className="text">Add product</span>
                  </a>
                ) : null}
                <div className='w-full h-full overflow-x-auto'>
                  <TableDealProducts
                    key={"services_"+this.state.tablesKey}
                    uid={this.props.uid + "_table_deal_services"}
                    className='mb-4'
                    data={{ data: R.SERVICES }}
                    descriptionSource='props'
                    customEndpointParams={{'idDeal': R.id}}
                    description={{
                      permissions: this.state.tableDealProductsDescription?.permissions,
                      columns: {
                        id_product: { type: "lookup", title: this.translate("Service"), model: "HubletoApp/Community/Products/Models/Product",
                          cellRenderer: ( table: TableDealProducts, data: any, options: any): JSX.Element => {
                            return (
                              <FormInput>
                                <Lookup {...this.getInputProps('services_input')}
                                  ref={this.refServicesLookup}
                                  model='HubletoApp/Community/Products/Models/Product'
                                  customEndpointParams={{'getServices': true}}
                                  cssClass='min-w-44'
                                  value={data.id_product}
                                  onChange={(input: any, value: any) => {
                                    getLookupData(this.refServicesLookup);
                                    if (lookupData[value]) {
                                      data.id_product = value;
                                      data.unit_price = lookupData[value].unit_price;
                                      data.vat = lookupData[value].vat;
                                      this.updateRecord({ SERVICES: table.state.data?.data });
                                      this.setState({tablesKey: Math.random()} as FormDealState)
                                    }
                                  }}
                                ></Lookup>
                              </FormInput>
                            )
                          },
                        },
                        unit_price: { type: "float", title: this.translate("Service Price"),},
                        amount: { type: "int", title: this.translate("Commitment") },
                        discount: { type: "float", title: this.translate("Discount (%)")},
                        vat: { type: "float", title: this.translate("Vat (%)")},
                        sum: { type: "float", title: this.translate("Sum")},
                      },
                      inputs: {
                        id_product: { type: "lookup", title: this.translate("Product"), model: "HubletoApp/Community/Products/Models/Product" },
                        unit_price: { type: "float", title: this.translate("Unit Price")},
                        amount: { type: "int", title: this.translate("Amount")},
                        vat: { type: "float", title: this.translate("Vat (%)")},
                        discount: { type: "float", title: this.translate("Discount (%)")},
                        sum: { type: "float", title: this.translate("Sum")},
                      },
                    }}
                    isUsedAsInput={true}
                    isInlineEditing={this.state.isInlineEditing}
                    readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                    onRowClick={() => this.setState({isInlineEditing: true})}
                    onChange={(table: TableDealProducts) => {
                      this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                    }}
                    onDeleteSelectionChange={(table: TableDealProducts) => {
                      this.updateRecord({ SERVICES: table.state.data?.data ?? []});
                      this.setState({tablesKey: Math.random()} as FormDealState)
                    }}
                  ></TableDealProducts>
                  <TableDealProducts
                    key={"products_"+this.state.tablesKey}
                    uid={this.props.uid + "_table_deal_products"}
                    data={{ data: R.PRODUCTS }}
                    descriptionSource='props'
                    customEndpointParams={{'idLead': R.id}}
                    description={{
                      permissions: this.state.tableDealProductsDescription?.permissions,
                      columns: {
                        id_product: { type: "lookup", title: this.translate("Product"), model: "HubletoApp/Community/Products/Models/Product",
                          cellRenderer: ( table: TableDealProducts, data: any, options: any): JSX.Element => {
                            return (
                              <FormInput>
                                <Lookup {...this.getInputProps('products_input')}
                                  ref={this.refProductsLookup}
                                  model='HubletoApp/Community/Products/Models/Product'
                                  customEndpointParams={{'getProducts': true}}
                                  cssClass='min-w-44'
                                  value={data.id_product}
                                  onChange={(input: any, value: any) => {
                                    getLookupData(this.refProductsLookup);
                                    if (lookupData[value]) {
                                      data.id_product = value;
                                      data.unit_price = lookupData[value].unit_price;
                                      data.vat = lookupData[value].vat;
                                      this.updateRecord({ PRODUCTS: table.state.data?.data });
                                      this.setState({tablesKey: Math.random()} as FormDealState)
                                    }
                                  }}
                                ></Lookup>
                              </FormInput>
                            )
                          },
                        },
                        unit_price: { type: "float", title: this.translate("Unit Price"),},
                        amount: { type: "int", title: this.translate("Amount") },
                        discount: { type: "float", title: this.translate("Discount (%)")},
                        vat: { type: "float", title: this.translate("Vat (%)")},
                        sum: { type: "float", title: this.translate("Sum")},
                      },
                      inputs: {
                        id_product: { type: "lookup", title: this.translate("Product"), model: "HubletoApp/Community/Products/Models/Product" },
                        unit_price: { type: "float", title: this.translate("Unit Price")},
                        amount: { type: "int", title: this.translate("Amount")},
                        vat: { type: "float", title: this.translate("Vat (%)")},
                        discount: { type: "float", title: this.translate("Discount (%)")},
                        sum: { type: "float", title: this.translate("Sum")},
                      },
                    }}
                    isUsedAsInput={true}
                    isInlineEditing={this.state.isInlineEditing}
                    readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                    onRowClick={() => this.setState({isInlineEditing: true})}
                    onChange={(table: TableDealProducts) => {
                      this.updateRecord({ PRODUCTS: table.state.data?.data ?? []});
                    }}
                    onDeleteSelectionChange={(table: TableDealProducts) => {
                      this.updateRecord({ PRODUCTS: table.state.data?.data ?? []});
                      this.setState({tablesKey: Math.random()} as FormDealState)
                    }}
                  ></TableDealProducts>
                </div>
              </div>
            </div>
          </div>
        </>;

      break;

      case 'calendar':
        return <>
          <Calendar
            onCreateCallback={() => this.loadRecord()}
            readonly={R.is_archived}
            initialView='timeGridWeek'
            views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
            eventsEndpoint={globalThis.main.config.rootUrl + '/calendar/api/get-calendar-events?source=deals&idDeal=' + R.id}
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
          ></Calendar>
        </>;
      break;

      // case 'tasks':
      //   try {
      //     return <>
      //       {this.state.id < 0 ?
      //           <div className="badge badge-info">{this.translate("First create the deal, then you will be prompted to add tasks.")}</div>
      //         :
      //           <TableTasks
      //             uid={this.props.uid + "_table_tasks"}
      //             tag={"DealTasks"}
      //             parentForm={this}
      //             externalModel='HubletoApp\Community\Deals\Models\Deal'
      //             externalId={R.id}
      //           />
      //       }
      //     </>;
      //   } catch (ex) {
      //     return <div className="alert alert-error">{this.translate("Failed to display tasks. Check if you have 'Tasks' app installed.")}</div>
      //   }
      // break;

      case 'documents':
        return <>
          <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
          {!R.is_archived ?
            <a
              className="btn btn-add-outline mb-2"
              onClick={() => this.setState({showIdDocument: -1} as FormDealState)}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">{this.translate("Add document")}</span>
            </a>
          : null}
          <TableDealDocuments
            key={this.state.tablesKey + "_table_deal_document"}
            uid={this.props.uid + "_table_deal_documents"}
            data={{ data: R.DOCUMENTS }}
            customEndpointParams={{idDeal: R.id}}
            descriptionSource="props"
            description={{
              permissions: this.state.tableDealDocumentsDescription?.permissions,
              ui: {
                showFooter: false,
                showHeader: false,
              },
              columns: {
                id_document: { type: "lookup", title: this.translate("Document"), model: "HubletoApp/Community/Documents/Models/Document" },
                hyperlink: { type: "varchar", title: this.translate("Link"), cellRenderer: ( table: TableDealDocuments, data: any, options: any): JSX.Element => {
                  return (
                    <FormInput>
                      <Hyperlink {...this.getInputProps('document-link')}
                        value={data.DOCUMENT.hyperlink ? data.DOCUMENT.hyperlink : null}
                        readonly={true}
                      ></Hyperlink>
                    </FormInput>
                  )
                },},
              },
              inputs: {
                id_document: { type: "lookup", title: this.translate("Document"), model: "HubletoApp/Community/Documents/Models/Document" },
                hyperlink: { type: "varchar", title: this.translate("Link"), readonly: true},
              }
            }}
            isUsedAsInput={true}
            readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
            onRowClick={(table: TableDealDocuments, row: any) => {
              this.setState({showIdDocument: row.id_document} as FormDealState);
            }}
            onDeleteSelectionChange={(table) => {
              this.updateRecord({ DOCUMENTS: table.state.data?.data ?? []});
              this.setState({tablesKey: Math.random()} as FormDealState)
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
                onClose={() => this.setState({showIdDocument: 0} as FormDealState)}
                showInModal={true}
                descriptionSource="both"
                description={{
                  defaultValues: {
                    creatingForModel: "HubletoApp/Community/Deals/Models/DealDocument",
                    creatingForId: this.state.record.id,
                    origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
                  }
                }}
                isInlineEditing={this.state.showIdDocument < 0 ? true : false}
                showInModalSimple={true}
                onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                  if (saveResponse.status = "success") {
                    this.loadRecord();
                    this.setState({ showIdDocument: 0 } as FormDealState)
                  }
                }}
                onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                  if (saveResponse.status = "success") {
                    this.loadRecord();
                    this.setState({ showIdDocument: 0 } as FormDealState)
                  }
                }}
              />
            </ModalForm>
          : null}
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
    }
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