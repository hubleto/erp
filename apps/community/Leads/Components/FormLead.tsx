import React, { Component, createRef, ChangeEvent } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import TableLeadProducts from './TableLeadProducts';
import { TabPanel, TabView } from 'primereact/tabview';
import Calendar from '../../Calendar/Components/Calendar';
import Lookup from 'adios/Inputs/Lookup';
import TableLeadDocuments from './TableLeadDocuments';
import ModalForm from 'adios/ModalForm';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import LeadFormActivity, { LeadFormActivityProps, LeadFormActivityState } from './LeadFormActivity';
import Hyperlink from 'adios/Inputs/Hyperlink';
import { FormProps, FormState } from 'adios/Form';
import { table } from 'console';
import moment, { Moment } from "moment";

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
  tableLeadProductsDescription: any,
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
  refServicesLookup: any;
  refProductsLookup: any;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

  constructor(props: FormLeadProps) {
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
      tableLeadProductsDescription: null,
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
        model: 'HubletoApp/Community/Leads/Models/LeadProduct',
        idLead: this.state.id,
      },
      (description: any) => {
        this.setState({tableLeadProductsDescription: description} as any);
      }
    );
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

  convertToDeal(recordId: number) {
    request.get(
      'leads/api/convert-to-deal',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          location.assign(globalThis.main.config.accountUrl + `/deals?recordId=${data.idDeal}&recordTitle=${data.title}`)
        }
      }
    );
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

  convertDealWarning(recordId: number) {
    globalThis.main.showDialogDanger(
      <>
        <div>
          Are you sure you want to convert this Lead to a Deal?<br/>
        </div>
        <div className="alert-warning mt-4">
          This lead will be moved to archive after conversion.<br/>
        </div>
      </>,
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to a Deal",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertToDeal(recordId)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to a Deal</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => {
              globalThis.main.lastShownDialogRef.current.hide();
            }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert to a Deal</span>
          </button>
        </>
      }
    );
  }

  moveToArchiveConfirm(recordId: number) {
    globalThis.main.showDialogConfirm(
      <div> Are you sure you want to move this lead to archive?</div>,
      {
        header: "Move to archive",
        yesText: "Yes, move to archive",
        onYes: () => this.moveToArchive(recordId),
        noText: "No, do not move to archive",
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

  renderContent(): JSX.Element {
    var lookupData;

    const getLookupData = (lookupElement) => {
      if (lookupElement.current) {
        lookupData = lookupElement.current.state.data;
      }
    }

    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (R.HISTORY && R.HISTORY.length > 0) {
      if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
        R.HISTORY = this.state.record.HISTORY.reverse();
    }

    if (R.DEAL) R.DEAL.checkOwnership = false;

    // if (R.id > 0 && globalThis.main.idUser != R.id_owner && !this.state.recordChanged) {
    //   return <>
    //     <div className='w-full h-full flex flex-col justify-center'>
    //       <span className='text-center'>This lead belongs to a different user</span>
    //     </div>
    //   </>;
    // }

    return (
      <>
        {this.state.id > 0 ?
          <div className="h-0 w-full text-right">
            <div className="badge badge-secondary badge-large">
              Lead value:&nbsp;{globalThis.main.numberFormat(R.price ?? 0, 2, ",", " ")} {R.CURRENCY.code}
            </div>
          </div>
        : null}
        <TabView>
          <TabPanel header={this.translate('Lead')}>
            {R.is_archived == 1 ?
              <div className='alert-warning mt-2 mb-1'>
                <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
                <span className='text'>This lead is archived.</span>
              </div>
            : null}
            <div className='flex gap-2 mt-2'>
              <div className='flex-2'>
                <div className='card card-body flex flex-row gap-2'>
                  <div className='grow'>
                    {this.inputWrapper('identifier', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
                    {this.inputWrapper('title', {cssClass: 'text-2xl text-primary', readonly: R.is_archived})}
                    <FormInput title={"Customer"}>
                      <Lookup {...this.getInputProps('id_customer')}
                        model='HubletoApp/Community/Customers/Models/Customer'
                        endpoint={`customers/get-customer`}
                        readonly={R.is_archived}
                        value={R.id_customer}
                        onChange={(value: any) => {
                          this.updateRecord({ id_customer: value, id_contact: null });
                          if (R.id_customer == 0) {
                            R.id_customer = null;
                            this.setState({record: R});
                          }
                        }}
                      ></Lookup>
                    </FormInput>
                    <FormInput title={"Contact"}>
                      <Lookup {...this.getInputProps('id_contact')}
                        model='HubletoApp/Community/Contacts/Models/Contact'
                        customEndpointParams={{id_customer: R.id_customer}}
                        readonly={R.is_archived}
                        endpoint={`contacts/get-customer-contacts`}
                        value={R.id_contact}
                        onChange={(value: any) => {
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
                      {this.inputWrapper('price', {
                        cssClass: 'text-2xl',
                        readonly: (R.PRODUCTS && R.PRODUCTS.length) > 0 || R.is_archived ? true : false,
                      })}
                      {this.inputWrapper('id_currency')}
                    </div>
                    {this.inputWrapper('status', {readonly: R.is_archived, onChange: () => {this.updateRecord({lost_reason: null})}})}
                    {this.state.record.status == 4 ? this.inputWrapper('lost_reason', {readonly: R.is_archived}): null}
                    {showAdditional ?
                      <div className='w-full mt-2 gap-2 flex'>
                        {R.DEAL != null ?
                        <a className='btn btn-primary' href={`${globalThis.main.config.url}/deals/${R.DEAL.id}`}>
                          <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                          <span className='text'>{this.translate('Go to deal')}</span>
                        </a>
                        :
                        <a className='btn btn-primary cursor-pointer' onClick={() => this.convertDealWarning(R.id)}>
                          <span className='icon'><i className='fas fa-rotate-right'></i></span>
                          <span className='text'>Convert to Deal</span>
                        </a>}
                        {R.is_archived ? null : <>
                          <a className='btn btn-transparent' onClick={() => this.moveToArchiveConfirm(R.id)}>
                            <span className='icon'><i className='fas fa-box-archive'></i></span>
                            <span className='text'>Move to archive</span>
                          </a>
                        </>}
                      </div>
                    : null}
                  </div>
                  <div className='border-l border-gray-200'></div>
                  <div className='grow'>
                    {this.inputWrapper('id_owner', {readonly: R.is_archived})}
                    {this.inputWrapper('id_responsible', {readonly: R.is_archived})}
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
                        onChange={(value: any) => {
                          R.TAGS = value;
                          this.setState({record: R});
                        }}
                      ></InputTags2>
                    </FormInput>
                    {showAdditional ? <>
                      {this.inputWrapper('date_created')}
                      {this.inputWrapper('is_archived')}
                    </> : null}
                  </div>
                </div>
                <div className='flex gap-2 mt-2 w-full'>
                  <div className='card grow'>
                    <div className='card-header'>Documents</div>
                    <div className='card-body'>
                      {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
                    </div>
                  </div>
                  <div className='card grow'>
                    <div className='card-header'>Notes</div>
                    <div className='card-body'>
                      {this.inputWrapper('note', {cssClass: 'bg-yellow-50', readonly: R.is_archived})}
                    </div>
                  </div>
                </div>
                {showAdditional ?
                  <div className='card mt-2'>
                    <div className='card-header'>Products & Services</div>
                    <div className='card-body'>
                      {this.state.isInlineEditing ?
                        <div className='text-yellow-500 mb-3'>
                          <span className='icon mr-2'><i className='fas fa-warning'></i></span>
                          <span className='text'>The sums of product and services prices will be updated after saving</span>
                        </div>
                      : <></>}
                      {!R.is_archived ? (
                        <a
                          className="btn btn-add-outline mb-2 mr-2"
                          onClick={() => {
                            if (!R.SERVICES) R.SERVICES = [];
                            R.SERVICES.push({
                              id: this.state.newEntryId,
                              id_lead: { _useMasterRecordId_: true },
                              amount: 1,
                            });
                            this.setState({ isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormLeadState);
                          }}
                        >
                          <span className="icon"><i className="fas fa-add"></i></span>
                          <span className="text">Add service</span>
                        </a>
                      ) : null}
                      {!R.is_archived ? (
                        <a
                          className="btn btn-add-outline mb-2"
                          onClick={() => {
                            if (!R.PRODUCTS) R.PRODUCTS = [];
                            R.PRODUCTS.push({
                              id: this.state.newEntryId,
                              id_lead: { _useMasterRecordId_: true },
                              amount: 1,
                            });
                            this.setState({ isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormLeadState);
                          }}
                        >
                          <span className="icon"><i className="fas fa-add"></i></span>
                          <span className="text">Add product</span>
                        </a>
                      ) : null}
                      <div className='w-full h-full overflow-x-auto'>
                        <TableLeadProducts
                          key={"services_"+this.state.tablesKey}
                          uid={this.props.uid + "_table_lead_services"}
                          className='mb-4'
                          data={{ data: R.SERVICES }}
                          descriptionSource='props'
                          customEndpointParams={{'idLead': R.id}}
                          description={{
                            permissions: this.state.tableLeadProductsDescription?.permissions,
                            columns: {
                              id_product: { type: "lookup", title: "Service", model: "HubletoApp/Community/Products/Models/Product",
                                cellRenderer: ( table: TableLeadProducts, data: any, options: any): JSX.Element => {
                                  return (
                                    <FormInput>
                                      <Lookup {...this.getInputProps('id_product_input_1')}
                                        ref={this.refServicesLookup}
                                        model='HubletoApp/Community/Products/Models/Product'
                                        customEndpointParams={{'getServices': true}}
                                        cssClass='min-w-44'
                                        value={data.id_product}
                                        onChange={(value: any) => {
                                          getLookupData(this.refServicesLookup);
                                          if (lookupData[value]) {
                                            data.id_product = value;
                                            data.unit_price = lookupData[value].unit_price;
                                            data.vat = lookupData[value].vat;
                                            this.updateRecord({ SERVICES: table.state.data?.data });
                                            this.setState({tablesKey: Math.random()} as FormLeadState)
                                          }
                                        }}
                                      ></Lookup>
                                    </FormInput>
                                  )
                                },
                              },
                              unit_price: { type: "float", title: "Service Price",},
                              amount: { type: "int", title: "Commitment" },
                              discount: { type: "float", title: "Discount (%)"},
                              vat: { type: "float", title: "Vat (%)"},
                              sum: { type: "float", title: "Sum"},
                            },
                            inputs: {
                              id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product" },
                              unit_price: { type: "float", title: "Unit Price"},
                              amount: { type: "int", title: "Amount"},
                              vat: { type: "float", title: "Vat (%)"},
                              discount: { type: "float", title: "Discount (%)"},
                              sum: { type: "float", title: "Sum"},
                            },
                          }}
                          isUsedAsInput={true}
                          isInlineEditing={this.state.isInlineEditing}
                          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                          onRowClick={() => this.setState({isInlineEditing: true})}
                          onChange={(table: TableLeadProducts) => {
                            this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                          }}
                          onDeleteSelectionChange={(table: TableLeadProducts) => {
                            this.updateRecord({ SERVICES: table.state.data?.data ?? []});
                            this.setState({tablesKey: Math.random()} as FormLeadState)
                          }}
                        ></TableLeadProducts>
                        <TableLeadProducts
                          key={"products_"+this.state.tablesKey}
                          uid={this.props.uid + "_table_lead_products"}
                          data={{ data: R.PRODUCTS }}
                          descriptionSource='props'
                          customEndpointParams={{'idLead': R.id}}
                          description={{
                            permissions: this.state.tableLeadProductsDescription?.permissions,
                            columns: {
                              id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                                cellRenderer: ( table: TableLeadProducts, data: any, options: any): JSX.Element => {
                                  return (
                                    <FormInput>
                                      <Lookup {...this.getInputProps('id_product_input_2')}
                                        ref={this.refProductsLookup}
                                        model='HubletoApp/Community/Products/Models/Product'
                                        customEndpointParams={{'getProducts': true}}
                                        cssClass='min-w-44'
                                        value={data.id_product}
                                        onChange={(value: any) => {
                                          getLookupData(this.refProductsLookup);
                                          if (lookupData[value]) {
                                            data.id_product = value;
                                            data.unit_price = lookupData[value].unit_price;
                                            data.vat = lookupData[value].vat;
                                            this.updateRecord({ PRODUCTS: table.state.data?.data });
                                            this.setState({tablesKey: Math.random()} as FormLeadState)
                                          }
                                        }}
                                      ></Lookup>
                                    </FormInput>
                                  )
                                },
                              },
                              unit_price: { type: "float", title: "Unit Price",},
                              amount: { type: "int", title: "Amount" },
                              discount: { type: "float", title: "Discount (%)"},
                              vat: { type: "float", title: "Vat (%)"},
                              sum: { type: "float", title: "Sum"},
                            },
                            inputs: {
                              id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product" },
                              unit_price: { type: "float", title: "Unit Price"},
                              amount: { type: "int", title: "Amount"},
                              vat: { type: "float", title: "Vat (%)"},
                              discount: { type: "float", title: "Discount (%)"},
                              sum: { type: "float", title: "Sum"},
                            },
                          }}
                          isUsedAsInput={true}
                          isInlineEditing={this.state.isInlineEditing}
                          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                          onRowClick={() => this.setState({isInlineEditing: true})}
                          onChange={(table: TableLeadProducts) => {
                            this.updateRecord({ PRODUCTS: table.state.data?.data ?? []});
                          }}
                          onDeleteSelectionChange={(table: TableLeadProducts) => {
                            this.updateRecord({ PRODUCTS: table.state.data?.data ?? []});
                            this.setState({tablesKey: Math.random()} as FormLeadState)
                          }}
                        ></TableLeadProducts>
                      </div>
                    </div>
                  </div>
                  : <></>}
              </div>
              {showAdditional ? <div className='flex-1'>
                <div className="card card-body border border-blue-200 shadow-blue-200">
                  <div className="adios component input"><div className="input-element w-full flex gap-2">
                    <input
                      className="w-full bg-blue-50 border border-blue-800 p-1 text-blue-800 placeholder-blue-300"
                      placeholder="Type recent activity here"
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
                      <span className="text">Enter = Log completed activity</span>
                    </button>
                    <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
                      <span className="icon"><i className="fas fa-clock"></i></span>
                      <span className="text">Shift+Enter = Schedule</span>
                    </button>
                  </div>
                  {this.divider('Most recent activities')}
                  {R.ACTIVITIES && R.ACTIVITIES.length > 0 ? <div className="list">{R.ACTIVITIES.reverse().slice(0, 10).map((item, index) => {
                    return <>
                      <button key={index} className="btn btn-small btn-transparent btn-list-item"
                        onClick={() => this.setState({showIdActivity: item.id} as FormLeadState)}
                      >
                        <span className="icon">{item.date_start} {item.time_start}<br/>{item['_LOOKUP[id_owner]']}</span>
                        <span className="text">
                          {item.subject}
                          {item.completed ? null : <div className="text-red-800">Not completed yet</div>}
                        </span>
                      </button>
                    </>
                  })}</div> : <>No activities for this lead yet.</>}
                </div>
                <div className="card card-body mt-2">
                  <Calendar
                    onCreateCallback={() => this.loadRecord()}
                    readonly={R.is_archived}
                    initialView='dayGridMonth'
                    headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
                    eventsEndpoint={globalThis.main.config.accountUrl + '/leads/get-calendar-events?idLead=' + R.id}
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
              </div> : null}
            </div>
          </TabPanel>
          {showAdditional ?
            <TabPanel header={this.translate('Calendar')}>
              <Calendar
                onCreateCallback={() => this.loadRecord()}
                readonly={R.is_archived}
                initialView='timeGridWeek'
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                eventsEndpoint={globalThis.main.config.accountUrl + '/leads/get-calendar-events?idLead=' + R.id}
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
            </TabPanel>
          : null}
          {showAdditional ? (
            <TabPanel header={this.translate("Documents")}>
              <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
              {!R.is_archived ?
                <a
                  className="btn btn-add-outline mb-2"
                  onClick={() => this.setState({showIdDocument: -1} as FormLeadState)}
                >
                  <span className="icon"><i className="fas fa-add"></i></span>
                  <span className="text">Add document</span>
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
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", cellRenderer: ( table: TableLeadDocuments, data: any, options: any): JSX.Element => {
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
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", readonly: true},
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
            </TabPanel>
          ) : null}
          {showAdditional ?
            <TabPanel header={this.translate('History')}>
              {R.HISTORY.length > 0 ?
                R.HISTORY.map((history, key) => (
                  <div key={key} className='w-full flex flex-row justify-between'>
                    <div className='w-1/3'>
                        <p className='font-bold self-center text-sm text-left'>
                          {history.description}
                        </p>
                      </div>
                    <div className='w-1/3' style={{alignContent: "center"}}>
                      <hr style={{width: "100%", alignSelf: "center"}}/>
                    </div>
                    <div className='w-1/3 justify-center'>
                      <p className='self-center text-sm text-center'>
                        {history.change_date}
                      </p>
                    </div>
                  </div>
                ))
                :
                <p className='text-gray-400'>Lead has no history</p>
              }
            </TabPanel>
          : null}
        </TabView>
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
    );
  }
}